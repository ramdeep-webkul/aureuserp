<?php

use Webkul\Attendance\Models\Attendance;
use Webkul\Employee\Models\Employee;

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
}
