<?php

namespace Webkul\Attendance\Livewire;

use Filament\Facades\Filament;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Webkul\Attendance\Enums\AttendanceMode;
use Webkul\Attendance\Exceptions\AttendanceAccessException;
use Webkul\Attendance\Exceptions\AttendanceValidationException;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\Overtime;
use Webkul\Attendance\Services\AttendanceWorkflow;
use Webkul\Attendance\Services\WorkedHoursCalculator;
use Webkul\Attendance\Settings\AttendanceSettings;
use Webkul\Employee\Models\Employee;
use Webkul\Security\Models\Scopes\OwnershipScope;
use Webkul\Support\Models\Scopes\CompanyScope;

class CheckInOut extends Component
{
    public function boot(): void
    {
        abort_unless(Filament::auth()->check(), 403);
    }

    #[Computed]
    public function employee(): ?Employee
    {
        return Employee::withoutGlobalScope(CompanyScope::class)
            ->where('user_id', Filament::auth()->id())
            ->first();
    }

    #[Computed]
    public function openAttendance(): ?Attendance
    {
        return $this->employee
            ? app(AttendanceWorkflow::class)->openAttendance($this->employee)
            : null;
    }

    public function isVisible(): bool
    {
        return $this->employee
            && app(AttendanceSettings::class)->enable_top_bar
            && Gate::allows('viewAny', Attendance::class);
    }

    public function hoursToday(): float
    {
        $workedHours = app(WorkedHoursCalculator::class);

        $start = now($workedHours->timezone($this->employee))->startOfDay()->utc();

        $closed = (float) Attendance::withoutGlobalScopes([CompanyScope::class, OwnershipScope::class])
            ->where('employee_id', $this->employee->id)
            ->whereNotNull('check_out')
            ->where('check_in', '>=', $start)
            ->sum('worked_hours');

        $open = $this->openAttendance
            ? $workedHours->hoursBetween($this->employee, $this->openAttendance->check_in, now())
            : 0.0;

        return $closed + $open;
    }

    public function overtimeBalance(): ?float
    {
        return app(AttendanceSettings::class)->display_overtime
            ? Overtime::balanceFor($this->employee)
            : null;
    }

    public function toggle(): void
    {
        if (! $this->isVisible()) {
            return;
        }

        $workflow = app(AttendanceWorkflow::class);

        $context = [
            'ip_address' => request()->ip(),
            'browser'    => substr((string) request()->userAgent(), 0, 255),
        ];

        try {
            $attendance = $this->openAttendance
                ? $workflow->checkOut($this->employee, AttendanceMode::TOP_BAR, context: $context)
                : $workflow->checkIn($this->employee, AttendanceMode::TOP_BAR, context: $context);
        } catch (AttendanceValidationException|AttendanceAccessException $e) {
            Notification::make()
                ->danger()
                ->title($e->getMessage())
                ->send();

            return;
        }

        unset($this->openAttendance);

        Notification::make()
            ->success()
            ->title($attendance->check_out
                ? __('attendances::livewire/check-in-out.notifications.checked-out')
                : __('attendances::livewire/check-in-out.notifications.checked-in'))
            ->send();
    }

    public function render(): View
    {
        return view('attendances::livewire.check-in-out');
    }
}
