<?php

namespace Webkul\Attendance\Services;

use Carbon\Carbon;
use Webkul\Attendance\Models\Attendance;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Models\Calendar;

class WorkedHoursCalculator
{
    public function calculate(Attendance $attendance): float
    {
        if (! $attendance->check_in || ! $attendance->check_out) {
            return 0.0;
        }

        return $this->hoursBetween($attendance->employee, $attendance->check_in, $attendance->check_out);
    }

    public function hoursBetween(Employee $employee, Carbon $from, Carbon $to): float
    {
        if ($to->lte($from)) {
            return 0.0;
        }

        $seconds = (float) $from->diffInSeconds($to);

        $calendar = $employee->calendar;

        if ($calendar && ! $this->isFlexible($employee)) {
            $timezone = $this->timezone($employee);

            $day = $from->copy()->setTimezone($timezone)->startOfDay();

            $lastDay = $to->copy()->setTimezone($timezone)->startOfDay();

            while ($day->lte($lastDay)) {
                foreach ($this->scheduleIntervals($calendar, $day->toDateString(), $timezone, lunch: true) as [$start, $stop]) {
                    $overlapStart = $start->max($from);

                    $overlapStop = $stop->min($to);

                    if ($overlapStart->lt($overlapStop)) {
                        $seconds -= (float) $overlapStart->diffInSeconds($overlapStop);
                    }
                }

                $day->addDay();
            }
        }

        return max(0.0, $seconds) / 3600;
    }

    public function timezone(Employee $employee): string
    {
        return $employee->time_zone
            ?: ($employee->calendar?->timezone ?: config('app.timezone'));
    }

    public function isFlexible(Employee $employee): bool
    {
        return (bool) ($employee->is_flexible || $employee->calendar?->flexible_hours);
    }

    public function localDate(Employee $employee, Carbon $moment): string
    {
        return $moment->copy()->setTimezone($this->timezone($employee))->toDateString();
    }

    public function scheduleIntervals(Calendar $calendar, string $date, string $timezone, bool $lunch = false): array
    {
        $start = Carbon::parse($date, 'UTC')->startOfDay();

        return $calendar
            ->getAttendanceIntervalsBatch($start, $start->copy()->addDay(), timezone: 'UTC', lunch: $lunch)[null]
            ->map(fn (array $interval) => [
                Carbon::parse($interval[0]->format('Y-m-d H:i:s'), $timezone)->utc(),
                Carbon::parse($interval[1]->format('Y-m-d H:i:s'), $timezone)->utc(),
            ])
            ->filter(fn (array $interval) => $interval[0]->lt($interval[1]))
            ->sortBy(fn (array $interval) => $interval[0]->getTimestamp())
            ->values()
            ->all();
    }
}
