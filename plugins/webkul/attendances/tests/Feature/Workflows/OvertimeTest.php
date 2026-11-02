<?php

require_once __DIR__.'/../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/AttendanceHelper.php';

use Webkul\Attendance\Enums\OvertimeStatus;
use Webkul\Attendance\Enums\OvertimeValidation;
use Webkul\Attendance\Models\Overtime;
use Webkul\Attendance\Services\AttendanceWorkflow;
use Webkul\Support\Models\CalendarLeave;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('attendances');

    SecurityHelper::disableUserEvents();

    $this->travelTo(AttendanceHelper::at('2026-10-17 12:00'));

    AttendanceHelper::settings([
        'overtime_company_threshold'  => 0,
        'overtime_employee_threshold' => 0,
        'overtime_validation'         => OvertimeValidation::NO_VALIDATION,
        'overtime_start_date'         => null,
    ]);
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function attendanceLedger($employee, string $date): ?Overtime
{
    return Overtime::query()
        ->where('employee_id', $employee->id)
        ->whereDate('date', $date)
        ->where('adjustment', false)
        ->first();
}

it('subtracts the schedule lunch from worked hours and books no overtime on a normal day', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    $attendance = AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 17:00');

    expect($attendance->worked_hours)->toBe(8.0)
        ->and($attendance->overtime_hours)->toBe(0.0)
        ->and($attendance->expected_hours)->toBe(8.0)
        ->and(attendanceLedger($employee, '2026-10-05'))->toBeNull();
});

it('books extra time after the planned end as overtime', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    $attendance = AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 19:00');

    expect($attendance->worked_hours)->toBe(10.0)
        ->and($attendance->overtime_hours)->toEqualWithDelta(2.0, 0.001)
        ->and($attendance->expected_hours)->toEqualWithDelta(8.0, 0.001)
        ->and(attendanceLedger($employee, '2026-10-05')->duration)->toEqualWithDelta(2.0, 0.001)
        ->and(attendanceLedger($employee, '2026-10-05')->duration_real)->toEqualWithDelta(2.0, 0.001);
});

it('ignores a late departure within the company threshold', function () {
    AttendanceHelper::settings(['overtime_company_threshold' => 15]);

    $employee = AttendanceHelper::scheduledEmployee();

    $attendance = AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 17:10');

    expect($attendance->overtime_hours)->toBe(0.0)
        ->and(attendanceLedger($employee, '2026-10-05'))->toBeNull();
});

it('counts a late departure beyond the company threshold', function () {
    AttendanceHelper::settings(['overtime_company_threshold' => 5]);

    $employee = AttendanceHelper::scheduledEmployee();

    $attendance = AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 17:10');

    expect($attendance->overtime_hours)->toEqualWithDelta(10 / 60, 0.001);
});

it('ignores a late arrival within the employee threshold', function () {
    AttendanceHelper::settings(['overtime_employee_threshold' => 15]);

    $employee = AttendanceHelper::scheduledEmployee();

    $attendance = AttendanceHelper::worked($employee, '2026-10-05 08:10', '2026-10-05 17:00');

    expect($attendance->overtime_hours)->toBe(0.0);
});

it('books a late arrival beyond the employee threshold as negative overtime', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    $attendance = AttendanceHelper::worked($employee, '2026-10-05 08:10', '2026-10-05 17:00');

    expect($attendance->overtime_hours)->toEqualWithDelta(-10 / 60, 0.001)
        ->and(attendanceLedger($employee, '2026-10-05')->duration)->toEqualWithDelta(-10 / 60, 0.001);
});

it('counts all time worked on a non-working day as overtime', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    $attendance = AttendanceHelper::worked($employee, '2026-10-10 10:00', '2026-10-10 14:00');

    expect($attendance->worked_hours)->toBe(4.0)
        ->and($attendance->overtime_hours)->toEqualWithDelta(4.0, 0.001);
});

it('counts all time worked on a public holiday as overtime', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    CalendarLeave::create([
        'name'      => 'Holiday',
        'time_type' => 'leave',
        'date_from' => '2026-10-05',
        'date_to'   => '2026-10-05',
    ]);

    $attendance = AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 17:00');

    expect($attendance->overtime_hours)->toEqualWithDelta(8.0, 0.001);
});

it('records hours without overtime for an employee without a schedule', function () {
    $employee = AttendanceHelper::employee();

    $attendance = AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 19:00');

    expect($attendance->worked_hours)->toBe(11.0)
        ->and($attendance->overtime_hours)->toBe(0.0)
        ->and(attendanceLedger($employee, '2026-10-05'))->toBeNull();
});

it('skips overtime for fully flexible employees', function () {
    $employee = AttendanceHelper::scheduledEmployee(attributes: ['is_fully_flexible' => true]);

    $attendance = AttendanceHelper::worked($employee, '2026-10-10 10:00', '2026-10-10 14:00');

    expect($attendance->overtime_hours)->toBe(0.0)
        ->and(attendanceLedger($employee, '2026-10-10'))->toBeNull();
});

it('compares flexible hours with the hours per day and keeps the lunch', function () {
    $employee = AttendanceHelper::scheduledEmployee(calendarAttributes: [
        'flexible_hours'           => true,
        'full_time_required_hours' => 0,
        'hours_per_day'            => 6,
    ]);

    $attendance = AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 17:00');

    expect($attendance->worked_hours)->toBe(9.0)
        ->and($attendance->overtime_hours)->toEqualWithDelta(3.0, 0.001);
});

it('caps the expected flexible hours by the weekly hours', function () {
    $employee = AttendanceHelper::scheduledEmployee(calendarAttributes: [
        'flexible_hours'           => true,
        'full_time_required_hours' => 20,
        'hours_per_day'            => 8,
    ]);

    AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 18:00');
    AttendanceHelper::worked($employee, '2026-10-06 08:00', '2026-10-06 18:00');
    AttendanceHelper::worked($employee, '2026-10-07 08:00', '2026-10-07 18:00');

    expect(attendanceLedger($employee, '2026-10-05')->duration)->toEqualWithDelta(2.0, 0.001)
        ->and(attendanceLedger($employee, '2026-10-06')->duration)->toEqualWithDelta(2.0, 0.001)
        ->and(attendanceLedger($employee, '2026-10-07')->duration)->toEqualWithDelta(6.0, 0.001);
});

it('books no overtime while a session of the day is still open', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    $workflow = app(AttendanceWorkflow::class);

    $morning = AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 12:00');

    $this->travelTo(AttendanceHelper::at('2026-10-05 13:00'));

    $workflow->checkIn($employee);

    expect(attendanceLedger($employee, '2026-10-05'))->toBeNull();

    $this->travelTo(AttendanceHelper::at('2026-10-05 19:00'));

    $afternoon = $workflow->checkOut($employee);

    expect(attendanceLedger($employee, '2026-10-05')->duration)->toEqualWithDelta(2.0, 0.001)
        ->and($afternoon->overtime_hours)->toEqualWithDelta(2.0, 0.001)
        ->and($morning->refresh()->overtime_hours)->toBe(0.0);
});

it('gives a shortfall to the newest session of the day', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    $morning = AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 12:00');

    $afternoon = AttendanceHelper::worked($employee, '2026-10-05 13:00', '2026-10-05 15:00');

    expect($afternoon->refresh()->overtime_hours)->toEqualWithDelta(-2.0, 0.001)
        ->and($afternoon->expected_hours)->toEqualWithDelta(4.0, 0.001)
        ->and($morning->refresh()->overtime_hours)->toBe(0.0);
});

it('reads the schedule in the employee time zone', function () {
    $employee = AttendanceHelper::scheduledEmployee('Asia/Kolkata');

    $normal = AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 17:00', 'Asia/Kolkata');

    $late = AttendanceHelper::worked($employee, '2026-10-06 08:00', '2026-10-06 19:00', 'Asia/Kolkata');

    expect($normal->worked_hours)->toBe(8.0)
        ->and($normal->overtime_hours)->toBe(0.0)
        ->and($late->overtime_hours)->toEqualWithDelta(2.0, 0.001);
});

it('books an overnight session on its check-in day', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    $attendance = AttendanceHelper::worked($employee, '2026-10-11 20:00', '2026-10-12 04:00');

    expect($attendance->worked_hours)->toBe(8.0)
        ->and(attendanceLedger($employee, '2026-10-11')->duration)->toEqualWithDelta(8.0, 0.001)
        ->and(attendanceLedger($employee, '2026-10-12'))->toBeNull();
});

it('books no overtime before the overtime start date', function () {
    AttendanceHelper::settings(['overtime_start_date' => '2026-10-06']);

    $employee = AttendanceHelper::scheduledEmployee();

    $before = AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 19:00');

    $after = AttendanceHelper::worked($employee, '2026-10-06 08:00', '2026-10-06 19:00');

    expect($before->overtime_hours)->toBe(0.0)
        ->and(attendanceLedger($employee, '2026-10-05'))->toBeNull()
        ->and($after->overtime_hours)->toEqualWithDelta(2.0, 0.001);
});

it('removes the ledger row when the attendance is deleted', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    $attendance = AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 19:00');

    app(AttendanceWorkflow::class)->delete($attendance);

    expect(attendanceLedger($employee, '2026-10-05'))->toBeNull();
});

it('recomputes both days when a correction moves the attendance', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    $attendance = AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 19:00');

    app(AttendanceWorkflow::class)->correct($attendance, [
        'check_in'  => AttendanceHelper::at('2026-10-10 10:00'),
        'check_out' => AttendanceHelper::at('2026-10-10 13:00'),
    ], 'Wrong day');

    expect(attendanceLedger($employee, '2026-10-05'))->toBeNull()
        ->and(attendanceLedger($employee, '2026-10-10')->duration)->toEqualWithDelta(3.0, 0.001)
        ->and($attendance->refresh()->overtime_hours)->toEqualWithDelta(3.0, 0.001);
});

it('approves overtime automatically without validation', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    $attendance = AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 19:00');

    expect($attendance->overtime_status)->toBe(OvertimeStatus::APPROVED)
        ->and($attendance->validated_overtime_hours)->toEqualWithDelta(2.0, 0.001);
});

it('waits for a decision when overtime is validated by a manager', function () {
    AttendanceHelper::settings(['overtime_validation' => OvertimeValidation::BY_MANAGER]);

    $employee = AttendanceHelper::scheduledEmployee();

    $workflow = app(AttendanceWorkflow::class);

    $attendance = AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 19:00');

    expect($attendance->overtime_status)->toBe(OvertimeStatus::TO_APPROVE)
        ->and($attendance->validated_overtime_hours)->toEqualWithDelta(2.0, 0.001);

    $workflow->refuseOvertime($attendance);

    expect($attendance->refresh()->validated_overtime_hours)->toBe(0.0);

    $workflow->approveOvertime($attendance);

    expect($attendance->refresh()->overtime_status)->toBe(OvertimeStatus::APPROVED)
        ->and($attendance->validated_overtime_hours)->toEqualWithDelta(2.0, 0.001);
});

it('keeps validated hours set by an approver when the attendance is recomputed', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    $workflow = app(AttendanceWorkflow::class);

    $attendance = AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 19:00');

    $workflow->approveOvertime($attendance, 1.5);

    $workflow->correct($attendance, ['check_out' => AttendanceHelper::at('2026-10-05 20:00')], 'Left later');

    expect($attendance->refresh()->overtime_hours)->toEqualWithDelta(3.0, 0.001)
        ->and($attendance->validated_overtime_hours)->toEqualWithDelta(1.5, 0.001);
});
