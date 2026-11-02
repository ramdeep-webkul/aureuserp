<?php

namespace Webkul\Attendance\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Webkul\Attendance\Enums\AttendanceMode;
use Webkul\Attendance\Enums\OvertimeStatus;
use Webkul\Attendance\Enums\OvertimeValidation;
use Webkul\Attendance\Events\AttendanceCompleted;
use Webkul\Attendance\Events\AttendanceCorrected;
use Webkul\Attendance\Events\OvertimeApproved;
use Webkul\Attendance\Exceptions\AttendanceAccessException;
use Webkul\Attendance\Exceptions\AttendanceValidationException;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Settings\AttendanceSettings;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Scopes\CompanyScope;

class AttendanceWorkflow
{
    public function __construct(protected AttendanceSettings $settings) {}

    public function checkIn(
        Employee $employee,
        AttendanceMode $mode = AttendanceMode::MANUAL,
        ?Carbon $at = null,
        array $context = [],
        ?string $idempotencyKey = null
    ): Attendance {
        return DB::transaction(function () use ($employee, $mode, $at, $context, $idempotencyKey): Attendance {
            $this->lockEmployee($employee);

            if ($idempotencyKey) {
                $existing = $this->attendances()->where('idempotency_key', $idempotencyKey)->first();

                if ($existing) {
                    return $existing;
                }
            }

            $attendance = new Attendance([
                'employee_id'     => $employee->id,
                'company_id'      => $employee->company_id,
                'check_in'        => ($at ?? now())->copy()->utc(),
                'in_mode'         => $mode,
                'in_ip_address'   => $context['ip_address'] ?? null,
                'in_browser'      => $context['browser'] ?? null,
                'overtime_status' => $this->initialOvertimeStatus(),
                'idempotency_key' => $idempotencyKey,
            ]);

            $attendance->setRelation('employee', $employee);

            $this->assertValid($attendance);

            $attendance->save();

            $this->recompute($attendance);

            return $attendance->refresh();
        });
    }

    public function checkOut(
        Employee $employee,
        AttendanceMode $mode = AttendanceMode::MANUAL,
        ?Carbon $at = null,
        array $context = []
    ): Attendance {
        $attendance = DB::transaction(function () use ($employee, $mode, $at, $context): Attendance {
            $this->lockEmployee($employee);

            $attendance = $this->openAttendance($employee);

            if (! $attendance) {
                throw new AttendanceValidationException(
                    __('attendances::system.workflow.not-checked-in', ['employee' => $employee->name])
                );
            }

            $attendance->fill([
                'check_out'      => ($at ?? now())->copy()->utc(),
                'out_mode'       => $mode,
                'out_ip_address' => $context['ip_address'] ?? null,
                'out_browser'    => $context['browser'] ?? null,
            ]);

            $this->assertValid($attendance);

            $this->saveWithHours($attendance);

            $this->recompute($attendance);

            return $attendance->refresh();
        });

        AttendanceCompleted::dispatch($attendance);

        return $attendance;
    }

    public function create(array $data): Attendance
    {
        $employee = $this->employee($data['employee_id']);

        $this->assertCanAssign($employee);

        $attendance = DB::transaction(function () use ($employee, $data): Attendance {
            $this->lockEmployee($employee);

            $attendance = new Attendance([
                'employee_id'     => $employee->id,
                'company_id'      => $employee->company_id,
                'check_in'        => Carbon::parse($data['check_in'])->utc(),
                'check_out'       => filled($data['check_out'] ?? null) ? Carbon::parse($data['check_out'])->utc() : null,
                'in_mode'         => AttendanceMode::MANUAL,
                'out_mode'        => filled($data['check_out'] ?? null) ? AttendanceMode::MANUAL : null,
                'edit_reason'     => $data['edit_reason'] ?? null,
                'overtime_status' => $this->initialOvertimeStatus(),
            ]);

            $attendance->setRelation('employee', $employee);

            $this->assertValid($attendance);

            $this->saveWithHours($attendance);

            $this->recompute($attendance);

            return $attendance->refresh();
        });

        if ($attendance->check_out) {
            AttendanceCompleted::dispatch($attendance);
        }

        return $attendance;
    }

    public function correct(Attendance $attendance, array $data, ?string $reason): Attendance
    {
        if (blank($reason)) {
            throw new AttendanceValidationException(__('attendances::system.workflow.reason-required'));
        }

        $previousEmployee = $attendance->employee;

        $previousDates = $this->datesOf($attendance);

        $employee = $previousEmployee;

        if (isset($data['employee_id']) && (int) $data['employee_id'] !== $attendance->employee_id) {
            $employee = $this->employee($data['employee_id']);

            $this->assertCanAssign($employee);
        }

        $attendance = DB::transaction(function () use ($attendance, $data, $reason, $employee, $previousEmployee, $previousDates): Attendance {
            $this->lockEmployee($employee);

            $attendance->employee_id = $employee->id;

            $attendance->company_id = $employee->company_id;

            $attendance->setRelation('employee', $employee);

            if (array_key_exists('check_in', $data)) {
                $checkIn = Carbon::parse($data['check_in'])->utc();

                if (! $attendance->check_in?->equalTo($checkIn) && $attendance->in_mode === AttendanceMode::TECHNICAL) {
                    $attendance->in_mode = AttendanceMode::MANUAL;
                }

                $attendance->check_in = $checkIn;
            }

            if (array_key_exists('check_out', $data)) {
                $checkOut = filled($data['check_out']) ? Carbon::parse($data['check_out'])->utc() : null;

                if ($checkOut && ! $attendance->check_out?->equalTo($checkOut) && $attendance->out_mode === AttendanceMode::TECHNICAL) {
                    $attendance->out_mode = AttendanceMode::MANUAL;
                }

                $attendance->check_out = $checkOut;

                $attendance->out_mode = $checkOut ? ($attendance->out_mode ?? AttendanceMode::MANUAL) : null;
            }

            $attendance->edit_reason = $reason;

            $this->assertValid($attendance);

            $this->saveWithHours($attendance);

            if ($previousEmployee->isNot($employee)) {
                app(OvertimeCalculator::class)->recompute($previousEmployee, $previousDates);

                $previousDates = [];
            }

            $this->recompute($attendance, $previousDates);

            return $attendance->refresh();
        });

        AttendanceCorrected::dispatch($attendance);

        return $attendance;
    }

    public function delete(Attendance $attendance): void
    {
        DB::transaction(function () use ($attendance): void {
            $this->lockEmployee($attendance->employee);

            $attendance->delete();

            app(OvertimeCalculator::class)->recompute($attendance->employee, $this->datesOf($attendance));
        });

        AttendanceCorrected::dispatch($attendance);
    }

    public function restore(Attendance $attendance): Attendance
    {
        $attendance = DB::transaction(function () use ($attendance): Attendance {
            $this->lockEmployee($attendance->employee);

            $this->assertValid($attendance);

            $attendance->restore();

            $this->recompute($attendance);

            return $attendance->refresh();
        });

        AttendanceCorrected::dispatch($attendance);

        return $attendance;
    }

    public function approveOvertime(Attendance $attendance, ?float $validatedHours = null): Attendance
    {
        $this->assertCanApprove($attendance);

        $attendance->update([
            'overtime_status'          => OvertimeStatus::APPROVED,
            'validated_overtime_hours' => $validatedHours
                ?? ($attendance->overtime_status === OvertimeStatus::REFUSED
                    ? $attendance->overtime_hours
                    : $attendance->validated_overtime_hours),
        ]);

        OvertimeApproved::dispatch($attendance);

        return $attendance;
    }

    public function refuseOvertime(Attendance $attendance): Attendance
    {
        $this->assertCanApprove($attendance);

        $attendance->update([
            'overtime_status'          => OvertimeStatus::REFUSED,
            'validated_overtime_hours' => 0,
        ]);

        return $attendance;
    }

    public function canApprove(?User $user, Attendance $attendance): bool
    {
        if (! $user) {
            return true;
        }

        return $user->resource_permission === PermissionType::GLOBAL
            || $attendance->employee?->attendance_manager_id === $user->id;
    }

    public function canAssign(?User $user, Employee $employee): bool
    {
        if (! $user) {
            return true;
        }

        return $user->resource_permission === PermissionType::GLOBAL
            || $employee->user_id === $user->id
            || $employee->attendance_manager_id === $user->id;
    }

    public function openAttendance(Employee $employee): ?Attendance
    {
        return $this->attendances()
            ->where('employee_id', $employee->id)
            ->whereNull('check_out')
            ->latest('check_in')
            ->first();
    }

    protected function assertValid(Attendance $attendance): void
    {
        $employee = $attendance->employee;

        if ($attendance->check_out && $attendance->check_out->lt($attendance->check_in)) {
            throw new AttendanceValidationException(__('attendances::system.workflow.check-out-before-check-in'));
        }

        if ($attendance->check_in->gt(now())) {
            throw new AttendanceValidationException(__('attendances::system.workflow.future-check-in'));
        }

        $others = $this->attendances()
            ->where('employee_id', $employee->id)
            ->when($attendance->exists, fn ($query) => $query->whereKeyNot($attendance->getKey()));

        $previous = (clone $others)
            ->where('check_in', '<=', $attendance->check_in)
            ->orderByDesc('check_in')
            ->first();

        $open = (clone $others)->whereNull('check_out')->first();

        if ($open && (! $attendance->check_out || $open->check_in->lt($attendance->check_out))) {
            throw new AttendanceValidationException(__('attendances::system.workflow.already-checked-in', [
                'employee' => $employee->name,
                'datetime' => $this->format($employee, $open->check_in),
            ]));
        }

        if ($previous && $previous->check_out?->gt($attendance->check_in)) {
            throw $this->overlap($employee, $previous);
        }

        if (! $attendance->check_out) {
            return;
        }

        $inside = (clone $others)
            ->where('check_in', '>', $attendance->check_in)
            ->where('check_in', '<', $attendance->check_out)
            ->orderBy('check_in')
            ->first();

        if ($inside) {
            throw $this->overlap($employee, $inside);
        }
    }

    protected function assertCanAssign(Employee $employee): void
    {
        if (! $this->canAssign(Auth::user(), $employee)) {
            throw new AttendanceAccessException(__('attendances::system.workflow.assign-denied', ['employee' => $employee->name]));
        }
    }

    protected function assertCanApprove(Attendance $attendance): void
    {
        if (! $this->canApprove(Auth::user(), $attendance)) {
            throw new AttendanceAccessException(__('attendances::system.workflow.approve-denied', ['employee' => $attendance->employee?->name]));
        }
    }

    protected function overlap(Employee $employee, Attendance $other): AttendanceValidationException
    {
        return new AttendanceValidationException(__('attendances::system.workflow.overlap', [
            'employee' => $employee->name,
            'datetime' => $this->format($employee, $other->check_in),
        ]));
    }

    protected function saveWithHours(Attendance $attendance): void
    {
        $attendance->worked_hours = app(WorkedHoursCalculator::class)->calculate($attendance);

        $attendance->save();
    }

    protected function recompute(Attendance $attendance, array $extraDates = []): void
    {
        app(OvertimeCalculator::class)->recompute(
            $attendance->employee,
            array_merge($extraDates, $this->datesOf($attendance))
        );
    }

    protected function datesOf(Attendance $attendance): array
    {
        $workedHours = app(WorkedHoursCalculator::class);

        return array_filter([
            $attendance->check_in ? $workedHours->localDate($attendance->employee, $attendance->check_in) : null,
            $attendance->check_out ? $workedHours->localDate($attendance->employee, $attendance->check_out) : null,
        ]);
    }

    protected function initialOvertimeStatus(): OvertimeStatus
    {
        return $this->settings->overtime_validation === OvertimeValidation::BY_MANAGER
            ? OvertimeStatus::TO_APPROVE
            : OvertimeStatus::APPROVED;
    }

    protected function lockEmployee(Employee $employee): void
    {
        Employee::withoutGlobalScopes()
            ->whereKey($employee->getKey())
            ->lockForUpdate()
            ->first();
    }

    protected function employee(int $id): Employee
    {
        return Employee::withoutGlobalScope(CompanyScope::class)->findOrFail($id);
    }

    protected function attendances()
    {
        return Attendance::withoutGlobalScope(CompanyScope::class);
    }

    protected function format(Employee $employee, Carbon $moment): string
    {
        return $moment->copy()
            ->setTimezone(app(WorkedHoursCalculator::class)->timezone($employee))
            ->format('Y-m-d H:i');
    }
}
