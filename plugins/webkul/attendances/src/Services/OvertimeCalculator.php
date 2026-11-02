<?php

namespace Webkul\Attendance\Services;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Webkul\Attendance\Enums\OvertimeStatus;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\Overtime;
use Webkul\Attendance\Settings\AttendanceSettings;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Models\Calendar;
use Webkul\Support\Models\CalendarLeave;
use Webkul\Support\Models\Scopes\CompanyScope;

class OvertimeCalculator
{
    protected const PRECISION = 0.005;

    public function __construct(protected AttendanceSettings $settings) {}

    public function recompute(Employee $employee, array $dates): void
    {
        $dates = array_values(array_unique(array_filter($dates)));

        if ($dates === []) {
            return;
        }

        $calendar = $employee->calendar;

        if ($calendar && $calendar->flexible_hours && $calendar->full_time_required_hours) {
            $dates = $this->expandToWeeks($dates);
        }

        sort($dates);

        $expectedPerWeek = [];

        foreach ($dates as $date) {
            $attendances = $this->attendancesOn($employee, $date);

            [$duration, $durationReal] = $this->dayOvertime($employee, $calendar, $date, $attendances, $expectedPerWeek);

            $this->storeLedger($employee, $date, $duration, $durationReal);

            $this->spread($attendances, $duration);
        }
    }

    protected function dayOvertime(Employee $employee, ?Calendar $calendar, string $date, Collection $attendances, array &$expectedPerWeek): array
    {
        if (
            ! $calendar
            || $employee->is_fully_flexible
            || $attendances->isEmpty()
            || $attendances->contains(fn (Attendance $attendance) => ! $attendance->check_out)
            || $this->beforeStartDate($date)
        ) {
            return [0.0, 0.0];
        }

        $hoursToday = (float) $attendances->sum('worked_hours');

        if ($calendar->flexible_hours && $calendar->full_time_required_hours) {
            $week = Carbon::parse($date)->startOfWeek(Carbon::MONDAY)->toDateString();

            $expectedSoFar = $expectedPerWeek[$week] ?? 0.0;

            $expectedToday = min(
                (float) $calendar->hours_per_day,
                max(0.0, (float) $calendar->full_time_required_hours - $expectedSoFar)
            );

            $expectedPerWeek[$week] = $expectedSoFar + $expectedToday;

            return [$hoursToday - $expectedToday, $hoursToday - $expectedToday];
        }

        if ($calendar->flexible_hours) {
            $overtime = $hoursToday - ((float) $calendar->hours_per_day ?: 8.0);

            return [$overtime, $overtime];
        }

        $workedHours = app(WorkedHoursCalculator::class);

        $timezone = $workedHours->timezone($employee);

        $planned = $this->subtractLeaves(
            $workedHours->scheduleIntervals($calendar, $date, $timezone),
            $this->leaveIntervals($employee, $calendar, $date, $timezone)
        );

        if ($planned === []) {
            return [$hoursToday, $hoursToday];
        }

        [$preWork, $work, $postWork, $plannedHours] = $this->prePostWorkTime($employee, $planned, $attendances);

        $total = $preWork + $work + $postWork - $plannedHours;

        $companyThreshold = $this->settings->overtime_company_threshold / 60;

        $employeeThreshold = $this->settings->overtime_employee_threshold / 60;

        $overtime = 0.0;

        if ($total > 0 && $total > $companyThreshold) {
            $overtime = $total;
        } elseif ($total < 0 && abs($total) > $employeeThreshold) {
            $overtime = $total;
        }

        return [$overtime, $hoursToday - $plannedHours];
    }

    protected function prePostWorkTime(Employee $employee, array $planned, Collection $attendances): array
    {
        $companyThreshold = $this->settings->overtime_company_threshold / 60;

        $employeeThreshold = $this->settings->overtime_employee_threshold / 60;

        $plannedStart = collect($planned)->map(fn (array $interval) => $interval[0])->min();

        $plannedEnd = collect($planned)->map(fn (array $interval) => $interval[1])->max();

        $plannedHours = collect($planned)->sum(fn (array $interval) => $interval[0]->diffInSeconds($interval[1]) / 3600);

        $workedHours = app(WorkedHoursCalculator::class);

        $preWork = $work = $postWork = 0.0;

        foreach ($attendances as $attendance) {
            $checkIn = $attendance->check_in->copy()->utc();

            $checkOut = $attendance->check_out->copy()->utc();

            $deltaIn = $checkIn->diffInSeconds($plannedStart) / 3600;

            if (
                ($deltaIn > 0 && $deltaIn <= $companyThreshold)
                || ($deltaIn < 0 && abs($deltaIn) <= $employeeThreshold)
            ) {
                $checkIn = $plannedStart->copy();
            }

            $deltaOut = $plannedEnd->diffInSeconds($checkOut) / 3600;

            if (
                ($deltaOut > 0 && $deltaOut <= $companyThreshold)
                || ($deltaOut < 0 && abs($deltaOut) <= $employeeThreshold)
            ) {
                $checkOut = $plannedEnd->copy();
            }

            if ($checkIn->lt($plannedStart)) {
                $preWork += $checkIn->diffInSeconds($plannedStart->min($checkOut)) / 3600;
            }

            if ($checkIn->lte($plannedEnd) && $checkOut->gte($plannedStart)) {
                $work += $workedHours->hoursBetween($employee, $plannedStart->max($checkIn), $plannedEnd->min($checkOut));
            }

            if ($checkOut->gt($plannedEnd)) {
                $postWork += $plannedEnd->max($checkIn)->diffInSeconds($checkOut) / 3600;
            }
        }

        return [$preWork, $work, $postWork, $plannedHours];
    }

    protected function leaveIntervals(Employee $employee, Calendar $calendar, string $date, string $timezone): array
    {
        return CalendarLeave::withoutGlobalScopes()
            ->where('time_type', 'leave')
            ->where(fn ($query) => $query->whereNull('calendar_id')->orWhere('calendar_id', $calendar->id))
            ->where(fn ($query) => $query->whereNull('company_id')->orWhere('company_id', $employee->company_id))
            ->where(fn ($query) => $query
                ->whereNull('resource_id')
                ->orWhere(fn ($query) => $query
                    ->where('resource_type', $employee->getMorphClass())
                    ->where('resource_id', $employee->id)))
            ->where('date_from', '<=', $date.' 23:59:59')
            ->where('date_to', '>=', $date)
            ->get()
            ->map(fn (CalendarLeave $leave) => [
                $this->leaveBound($leave->date_from, $timezone, false),
                $this->leaveBound($leave->date_to, $timezone, true),
            ])
            ->all();
    }

    protected function leaveBound(string $value, string $timezone, bool $isEnd): Carbon
    {
        // Public holidays are saved as plain dates and cover the whole day.
        if (strlen($value) <= 10) {
            $day = Carbon::parse($value, $timezone)->startOfDay();

            return ($isEnd ? $day->addDay() : $day)->utc();
        }

        return Carbon::parse($value, 'UTC');
    }

    protected function subtractLeaves(array $intervals, array $leaves): array
    {
        foreach ($leaves as [$leaveStart, $leaveStop]) {
            $remaining = [];

            foreach ($intervals as [$start, $stop]) {
                if ($start->lt($leaveStart)) {
                    $remaining[] = [$start, $stop->min($leaveStart)];
                }

                if ($stop->gt($leaveStop)) {
                    $remaining[] = [$start->max($leaveStop), $stop];
                }
            }

            $intervals = array_values(array_filter($remaining, fn (array $interval) => $interval[0]->lt($interval[1])));
        }

        return $intervals;
    }

    protected function storeLedger(Employee $employee, string $date, float $duration, float $durationReal): void
    {
        $overtime = Overtime::withoutGlobalScope(CompanyScope::class)
            ->where('employee_id', $employee->id)
            ->whereDate('date', $date)
            ->where('adjustment', false)
            ->first();

        if (abs($duration) < self::PRECISION) {
            $overtime?->delete();

            return;
        }

        $values = [
            'duration'      => $duration,
            'duration_real' => $durationReal,
        ];

        if ($overtime) {
            $overtime->update($values);

            return;
        }

        Overtime::create([
            'employee_id' => $employee->id,
            'company_id'  => $employee->company_id,
            'date'        => $date,
            'adjustment'  => false,
            ...$values,
        ]);
    }

    protected function spread(Collection $attendances, float $duration): void
    {
        $reservoir = $duration;

        foreach ($attendances->sortByDesc(fn (Attendance $attendance) => $attendance->check_in->getTimestamp())->values() as $index => $attendance) {
            $overtime = 0.0;

            if ($reservoir > 0) {
                $overtime = min((float) $attendance->worked_hours, $reservoir);

                $reservoir -= $overtime;
            } elseif ($reservoir < 0 && $index === 0) {
                $overtime = $reservoir;
            }

            $this->applyOvertime($attendance, $overtime);
        }
    }

    protected function applyOvertime(Attendance $attendance, float $overtime): void
    {
        $changedByApprover = abs((float) $attendance->validated_overtime_hours - (float) $attendance->overtime_hours) >= self::PRECISION;

        $attendance->overtime_hours = $overtime;

        $attendance->expected_hours = (float) $attendance->worked_hours - $overtime;

        if ($attendance->overtime_status === OvertimeStatus::REFUSED) {
            $attendance->validated_overtime_hours = 0.0;
        } elseif (! $changedByApprover) {
            $attendance->validated_overtime_hours = $overtime;
        }

        $attendance->saveQuietly();
    }

    protected function attendancesOn(Employee $employee, string $date): Collection
    {
        $start = Carbon::parse($date, app(WorkedHoursCalculator::class)->timezone($employee))->utc();

        return Attendance::withoutGlobalScope(CompanyScope::class)
            ->where('employee_id', $employee->id)
            ->where('check_in', '>=', $start)
            ->where('check_in', '<', $start->copy()->addDay())
            ->get();
    }

    protected function beforeStartDate(string $date): bool
    {
        return $this->settings->overtime_start_date
            && $date < Carbon::parse($this->settings->overtime_start_date)->toDateString();
    }

    protected function expandToWeeks(array $dates): array
    {
        $expanded = [];

        foreach ($dates as $date) {
            $day = Carbon::parse($date)->startOfWeek(Carbon::MONDAY);

            for ($i = 0; $i < 7; $i++) {
                $expanded[] = $day->toDateString();

                $day->addDay();
            }
        }

        return array_values(array_unique($expanded));
    }
}
