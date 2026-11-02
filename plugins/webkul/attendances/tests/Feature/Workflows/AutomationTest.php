<?php

require_once __DIR__.'/../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/AttendanceHelper.php';

use Webkul\Attendance\Enums\AttendanceMode;
use Webkul\Attendance\Enums\OvertimeValidation;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Services\AttendanceWorkflow;
use Webkul\Employee\Models\Employee;
use Webkul\Support\Models\CalendarLeave;
use Webkul\Support\Models\Company;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('attendances');

    SecurityHelper::disableUserEvents();

    AttendanceHelper::settings([
        'auto_check_out'              => true,
        'auto_check_out_tolerance'    => 2,
        'absence_management'          => true,
        'overtime_company_threshold'  => 0,
        'overtime_employee_threshold' => 0,
        'overtime_validation'         => OvertimeValidation::NO_VALIDATION,
        'overtime_start_date'         => null,
    ]);
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function attendanceCheckedInAt(string $checkIn, string $now, ?Employee $employee = null): Attendance
{
    $employee ??= AttendanceHelper::scheduledEmployee();

    test()->travelTo(AttendanceHelper::at($now));

    return app(AttendanceWorkflow::class)->checkIn($employee, at: AttendanceHelper::at($checkIn));
}

it('checks out a session that ran past the schedule and the tolerance', function () {
    $attendance = attendanceCheckedInAt('2026-10-16 08:00', '2026-10-16 20:00');

    $this->artisan('attendances:auto-check-out')->assertSuccessful();

    $attendance->refresh();

    expect($attendance->check_out->toDateTimeString())->toBe('2026-10-16 19:00:00')
        ->and($attendance->out_mode)->toBe(AttendanceMode::AUTO_CHECK_OUT)
        ->and($attendance->messages()->where('body', __('attendances::system.workflow.auto-checked-out'))->exists())->toBeTrue();
});

it('counts earlier sessions of the day before checking out', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    $this->travelTo(AttendanceHelper::at('2026-10-16 20:00'));

    AttendanceHelper::worked($employee, '2026-10-16 06:00', '2026-10-16 07:00');

    $attendance = attendanceCheckedInAt('2026-10-16 08:00', '2026-10-16 20:00', $employee);

    $this->artisan('attendances:auto-check-out')->assertSuccessful();

    expect($attendance->refresh()->check_out->toDateTimeString())->toBe('2026-10-16 18:00:00');
});

it('leaves a session open while it is within the tolerance', function () {
    $attendance = attendanceCheckedInAt('2026-10-16 08:00', '2026-10-16 18:30');

    $this->artisan('attendances:auto-check-out')->assertSuccessful();

    expect($attendance->refresh()->check_out)->toBeNull();
});

it('does not auto check out when the setting is off or the employee is flexible', function () {
    $flexible = attendanceCheckedInAt(
        '2026-10-16 08:00',
        '2026-10-16 23:00',
        AttendanceHelper::scheduledEmployee(attributes: ['is_flexible' => true])
    );

    $this->artisan('attendances:auto-check-out')->assertSuccessful();

    expect($flexible->refresh()->check_out)->toBeNull();

    AttendanceHelper::settings(['auto_check_out' => false]);

    $regular = attendanceCheckedInAt('2026-10-16 08:00', '2026-10-16 23:00');

    $this->artisan('attendances:auto-check-out')->assertSuccessful();

    expect($regular->refresh()->check_out)->toBeNull();
});

it('reads the auto check-out setting of each company', function () {
    AttendanceHelper::settings(['auto_check_out' => false]);

    $company = Company::factory()->create();

    AttendanceWorkflow::forCompany($company, fn () => AttendanceHelper::settings(['auto_check_out' => true]));

    $defaultCompany = attendanceCheckedInAt('2026-10-16 08:00', '2026-10-16 23:00');

    $otherCompany = attendanceCheckedInAt(
        '2026-10-16 08:00',
        '2026-10-16 23:00',
        AttendanceHelper::scheduledEmployee(attributes: ['company_id' => $company->id])
    );

    $this->artisan('attendances:auto-check-out')->assertSuccessful();

    expect($defaultCompany->refresh()->check_out)->toBeNull()
        ->and($otherCompany->refresh()->check_out)->not->toBeNull();
});

it('records an absence for a scheduled day without attendance', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    $this->travelTo(AttendanceHelper::at('2026-10-17 12:00'));

    $this->artisan('attendances:detect-absences')->assertSuccessful();

    $attendance = Attendance::where('employee_id', $employee->id)->sole();

    expect($attendance->check_in->toDateTimeString())->toBe('2026-10-16 00:00:00')
        ->and($attendance->check_out->toDateTimeString())->toBe('2026-10-16 00:00:01')
        ->and($attendance->in_mode)->toBe(AttendanceMode::TECHNICAL)
        ->and($attendance->overtime_hours)->toEqualWithDelta(-8.0, 0.01)
        ->and($attendance->messages()->where('body', __('attendances::system.workflow.absence-detected'))->exists())->toBeTrue();

    $this->artisan('attendances:detect-absences')->assertSuccessful();

    expect(Attendance::where('employee_id', $employee->id)->count())->toBe(1);
});

it('records no absence on a day off, a public holiday, a worked day or when the setting is off', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    $this->travelTo(AttendanceHelper::at('2026-10-19 12:00'));

    $this->artisan('attendances:detect-absences')->assertSuccessful();

    CalendarLeave::create([
        'name'      => 'Holiday',
        'time_type' => 'leave',
        'date_from' => '2026-10-19',
        'date_to'   => '2026-10-19',
    ]);

    $this->travelTo(AttendanceHelper::at('2026-10-20 12:00'));

    $this->artisan('attendances:detect-absences')->assertSuccessful();

    expect(Attendance::withTrashed()->where('employee_id', $employee->id)->count())->toBe(0);

    AttendanceHelper::worked($employee, '2026-10-20 08:00', '2026-10-20 17:00');

    $this->travelTo(AttendanceHelper::at('2026-10-21 12:00'));

    $this->artisan('attendances:detect-absences')->assertSuccessful();

    AttendanceHelper::settings(['absence_management' => false]);

    $this->travelTo(AttendanceHelper::at('2026-10-22 12:00'));

    $this->artisan('attendances:detect-absences')->assertSuccessful();

    expect(Attendance::withTrashed()->where('employee_id', $employee->id)->count())->toBe(1);
});

it('closes the open session when the employee is archived', function () {
    $attendance = attendanceCheckedInAt('2026-10-16 08:00', '2026-10-16 12:00');

    $attendance->employee->delete();

    $attendance->refresh();

    expect($attendance->check_out->toDateTimeString())->toBe('2026-10-16 12:00:00')
        ->and($attendance->worked_hours)->toEqualWithDelta(4.0, 0.01);
});
