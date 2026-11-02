<?php

use Carbon\Carbon;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Services\AttendanceWorkflow;
use Webkul\Attendance\Settings\AttendanceSettings;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Models\Calendar;
use Webkul\Support\Models\CalendarAttendance;

class AttendanceHelper
{
    /**
     * Create a minimal employee.
     *
     * Employee::factory() builds a department, a job position and a work location
     * along with it, so a direct create stays cheaper for tests that only need the row.
     */
    public static function employee(array $attributes = []): Employee
    {
        return Employee::create(array_merge([
            'name' => 'Test Employee',
        ], $attributes));
    }

    /**
     * Create an employee on a Monday-to-Friday schedule (08:00-12:00 and
     * 13:00-17:00, lunch 12:00-13:00) in the given time zone.
     */
    public static function scheduledEmployee(string $timezone = 'UTC', array $calendarAttributes = [], array $attributes = []): Employee
    {
        $calendar = static::calendar($timezone, $calendarAttributes);

        return static::employee(array_merge([
            'calendar_id' => $calendar->id,
            'time_zone'   => $timezone,
        ], $attributes));
    }

    /**
     * Create a working schedule. Each row is [day, period, from, to].
     */
    public static function calendar(string $timezone = 'UTC', array $attributes = [], ?array $rows = null): Calendar
    {
        $calendar = Calendar::create(array_merge([
            'name'                     => 'Standard 40 hours',
            'timezone'                 => $timezone,
            'hours_per_day'            => 8,
            'is_active'                => true,
            'two_weeks_calendar'       => false,
            'flexible_hours'           => false,
            'full_time_required_hours' => 40,
        ], $attributes));

        $rows ??= collect(['monday', 'tuesday', 'wednesday', 'thursday', 'friday'])
            ->flatMap(fn (string $day) => [
                [$day, 'morning', 8, 12],
                [$day, 'lunch', 12, 13],
                [$day, 'afternoon', 13, 17],
            ])
            ->all();

        foreach ($rows as [$day, $period, $from, $to]) {
            CalendarAttendance::create([
                'calendar_id' => $calendar->id,
                'name'        => ucfirst($day).' '.$period,
                'day_of_week' => $day,
                'day_period'  => $period,
                'hour_from'   => $from,
                'hour_to'     => $to,
            ]);
        }

        return $calendar;
    }

    /**
     * Create a closed attendance for an employee, creating the employee when one
     * is not supplied.
     */
    public static function attendance(array $attributes = [], ?Employee $employee = null): Attendance
    {
        $employee ??= static::employee();

        return Attendance::factory()->create(array_merge([
            'employee_id' => $employee->id,
        ], $attributes));
    }

    public static function at(string $datetime, string $timezone = 'UTC'): Carbon
    {
        return Carbon::parse($datetime, $timezone)->utc();
    }

    public static function worked(Employee $employee, string $from, string $to, string $timezone = 'UTC'): Attendance
    {
        return app(AttendanceWorkflow::class)->create([
            'employee_id' => $employee->id,
            'check_in'    => static::at($from, $timezone),
            'check_out'   => static::at($to, $timezone),
        ]);
    }

    public static function settings(array $values): AttendanceSettings
    {
        $settings = app(AttendanceSettings::class);

        foreach ($values as $key => $value) {
            $settings->{$key} = $value;
        }

        $settings->save();

        return $settings;
    }
}
