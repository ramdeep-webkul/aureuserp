<?php

require_once __DIR__.'/../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/CompanyHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/AttendanceHelper.php';

use Illuminate\Support\Facades\Event;
use Webkul\Attendance\Enums\AttendanceMode;
use Webkul\Attendance\Enums\OvertimeValidation;
use Webkul\Attendance\Events\AttendanceCompleted;
use Webkul\Attendance\Events\AttendanceCorrected;
use Webkul\Attendance\Events\OvertimeApproved;
use Webkul\Attendance\Exceptions\AttendanceAccessException;
use Webkul\Attendance\Exceptions\AttendanceValidationException;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\Overtime;
use Webkul\Attendance\Services\AttendanceWorkflow;
use Webkul\Security\Enums\PermissionType;

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

    $this->workflow = app(AttendanceWorkflow::class);
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('checks an employee in and out', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    $this->travelTo(AttendanceHelper::at('2026-10-12 08:00'));

    $attendance = $this->workflow->checkIn($employee, AttendanceMode::TOP_BAR, context: [
        'ip_address' => '10.0.0.1',
        'browser'    => 'Firefox',
    ]);

    expect($attendance->check_out)->toBeNull()
        ->and($attendance->in_mode)->toBe(AttendanceMode::TOP_BAR)
        ->and($attendance->in_ip_address)->toBe('10.0.0.1')
        ->and($attendance->company_id)->toBe($employee->company_id)
        ->and($this->workflow->openAttendance($employee)?->id)->toBe($attendance->id);

    $this->travelTo(AttendanceHelper::at('2026-10-12 17:00'));

    $attendance = $this->workflow->checkOut($employee, AttendanceMode::TOP_BAR);

    expect($attendance->check_out)->not->toBeNull()
        ->and($attendance->out_mode)->toBe(AttendanceMode::TOP_BAR)
        ->and($attendance->worked_hours)->toBe(8.0)
        ->and($this->workflow->openAttendance($employee))->toBeNull();
});

it('refuses a second check-in while a session is open', function () {
    $employee = AttendanceHelper::employee();

    $this->workflow->checkIn($employee);

    $this->workflow->checkIn($employee);
})->throws(AttendanceValidationException::class);

it('refuses a check-out without an open session', function () {
    $this->workflow->checkOut(AttendanceHelper::employee());
})->throws(AttendanceValidationException::class);

it('refuses a check-out earlier than the check-in', function () {
    $employee = AttendanceHelper::employee();

    $this->workflow->checkIn($employee);

    $this->workflow->checkOut($employee, at: now()->subHour());
})->throws(AttendanceValidationException::class);

it('refuses a check-in in the future', function () {
    $this->workflow->checkIn(AttendanceHelper::employee(), at: now()->addMinute());
})->throws(AttendanceValidationException::class);

it('refuses an attendance that starts inside another one', function () {
    $employee = AttendanceHelper::employee();

    AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 12:00');

    AttendanceHelper::worked($employee, '2026-10-05 11:00', '2026-10-05 13:00');
})->throws(AttendanceValidationException::class);

it('refuses an attendance that contains another one', function () {
    $employee = AttendanceHelper::employee();

    AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 12:00');

    AttendanceHelper::worked($employee, '2026-10-05 07:00', '2026-10-05 09:00');
})->throws(AttendanceValidationException::class);

it('allows back-to-back attendances', function () {
    $employee = AttendanceHelper::employee();

    AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 12:00');

    $second = AttendanceHelper::worked($employee, '2026-10-05 12:00', '2026-10-05 13:00');

    expect($second->exists)->toBeTrue();
});

it('returns the same attendance when a check-in is retried with its key', function () {
    $employee = AttendanceHelper::employee();

    $first = $this->workflow->checkIn($employee, idempotencyKey: 'retry-1');

    $retry = $this->workflow->checkIn($employee, idempotencyKey: 'retry-1');

    expect($retry->id)->toBe($first->id)
        ->and(Attendance::where('employee_id', $employee->id)->count())->toBe(1);
});

it('requires a reason to correct an attendance', function () {
    $employee = AttendanceHelper::employee();

    $attendance = AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 12:00');

    $this->workflow->correct($attendance, ['check_out' => AttendanceHelper::at('2026-10-05 13:00')], '');
})->throws(AttendanceValidationException::class);

it('switches a corrected technical side to manual', function () {
    $employee = AttendanceHelper::employee();

    $attendance = AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 08:00:01');

    $attendance->updateQuietly(['in_mode' => AttendanceMode::TECHNICAL, 'out_mode' => AttendanceMode::TECHNICAL]);

    $attendance = $this->workflow->correct($attendance, [
        'check_out' => AttendanceHelper::at('2026-10-05 12:00'),
    ], 'Forgot to check out');

    expect($attendance->in_mode)->toBe(AttendanceMode::TECHNICAL)
        ->and($attendance->out_mode)->toBe(AttendanceMode::MANUAL)
        ->and($attendance->edit_reason)->toBe('Forgot to check out')
        ->and($attendance->worked_hours)->toEqualWithDelta(4.0, 0.001);
});

it('lets the attendance manager move an attendance to their employee', function () {
    $user = SecurityHelper::authenticateWithPermissions([]);

    $source = AttendanceHelper::employee(['attendance_manager_id' => $user->id]);

    $target = AttendanceHelper::employee(['attendance_manager_id' => $user->id]);

    $attendance = AttendanceHelper::worked($source, '2026-10-05 08:00', '2026-10-05 12:00');

    $attendance = $this->workflow->correct($attendance, ['employee_id' => $target->id], 'Wrong employee');

    expect($attendance->employee_id)->toBe($target->id);
});

it('refuses to move an attendance to an employee the user does not manage', function () {
    $user = SecurityHelper::authenticateWithPermissions([]);

    $source = AttendanceHelper::employee(['attendance_manager_id' => $user->id]);

    $attendance = AttendanceHelper::worked($source, '2026-10-05 08:00', '2026-10-05 12:00');

    $this->workflow->correct($attendance, ['employee_id' => AttendanceHelper::employee()->id], 'Wrong employee');
})->throws(AttendanceAccessException::class);

it('lets a user with access to all records decide on overtime', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    $attendance = AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 19:00');

    $user = SecurityHelper::authenticateWithPermissions([]);

    $user->forceFill(['resource_permission' => PermissionType::GLOBAL])->saveQuietly();

    $this->workflow->refuseOvertime($attendance);

    expect($attendance->refresh()->validated_overtime_hours)->toBe(0.0);
});

it('refuses an overtime decision from a user who is not the approver', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    $attendance = AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 19:00');

    $user = SecurityHelper::authenticateWithPermissions([]);

    $user->forceFill(['resource_permission' => PermissionType::INDIVIDUAL])->saveQuietly();

    $this->workflow->approveOvertime($attendance);
})->throws(AttendanceAccessException::class);

it('dispatches the attendance events', function () {
    Event::fake([AttendanceCompleted::class, AttendanceCorrected::class, OvertimeApproved::class]);

    $employee = AttendanceHelper::scheduledEmployee();

    $this->travelTo(AttendanceHelper::at('2026-10-12 08:00'));

    $this->workflow->checkIn($employee);

    $this->travelTo(AttendanceHelper::at('2026-10-12 19:00'));

    $attendance = $this->workflow->checkOut($employee);

    $this->workflow->correct($attendance, ['check_out' => AttendanceHelper::at('2026-10-12 18:00')], 'Left earlier');

    $this->workflow->approveOvertime($attendance);

    Event::assertDispatched(AttendanceCompleted::class, fn ($event) => $event->attendance->is($attendance));
    Event::assertDispatched(AttendanceCorrected::class, fn ($event) => $event->attendance->is($attendance));
    Event::assertDispatched(OvertimeApproved::class, fn ($event) => $event->attendance->is($attendance));
});

it('restores a deleted attendance and its overtime', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    $attendance = AttendanceHelper::worked($employee, '2026-10-05 08:00', '2026-10-05 19:00');

    $this->workflow->delete($attendance);

    $this->workflow->restore($attendance);

    expect(Attendance::find($attendance->id))->not->toBeNull()
        ->and(Overtime::where('employee_id', $employee->id)->sum('duration'))->toEqualWithDelta(2.0, 0.001);
});

it('keeps attendances and overtime in the employee company', function () {
    $companyA = CompanyHelper::company();

    $companyB = CompanyHelper::company();

    $employee = AttendanceHelper::scheduledEmployee(attributes: ['company_id' => $companyB->id]);

    CompanyHelper::actingAsCompanyUser($companyA)
        ->forceFill(['resource_permission' => PermissionType::GLOBAL])
        ->saveQuietly();

    $attendance = AttendanceHelper::worked($employee, '2026-10-10 10:00', '2026-10-10 14:00');

    $overtime = Overtime::withoutGlobalScopes()->where('employee_id', $employee->id)->first();

    expect($attendance->company_id)->toBe($companyB->id)
        ->and($overtime->company_id)->toBe($companyB->id)
        ->and(Attendance::query()->whereKey($attendance->id)->exists())->toBeFalse();
});
