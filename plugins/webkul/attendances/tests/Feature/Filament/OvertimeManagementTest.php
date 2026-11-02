<?php

require_once __DIR__.'/../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/AttendanceHelper.php';

use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Attendance\Enums\OvertimeStatus;
use Webkul\Attendance\Enums\OvertimeValidation;
use Webkul\Attendance\Filament\Clusters\Management\Resources\OvertimeApprovalResource\Pages\ListOvertimeApprovals;
use Webkul\Attendance\Filament\Clusters\Management\Resources\OvertimeResource\Pages\ListOvertimes;
use Webkul\Attendance\Models\Overtime;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('attendances');

    SecurityHelper::disableUserEvents();

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    $this->travelTo('2026-10-17 12:00');

    AttendanceHelper::settings([
        'overtime_validation'         => OvertimeValidation::BY_MANAGER,
        'overtime_company_threshold'  => 0,
        'overtime_employee_threshold' => 0,
        'overtime_start_date'         => null,
    ]);

    FilamentHelper::actingAs([
        'view_any_attendance_attendance',
        'view_attendance_attendance',
        'update_attendance_attendance',
        'view_any_attendance_overtime',
        'view_attendance_overtime',
        'create_attendance_overtime',
        'update_attendance_overtime',
        'delete_attendance_overtime',
        'delete_any_attendance_overtime',
    ]);
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('shows only overtime waiting for approval by default', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    $toApprove = AttendanceHelper::worked($employee, '2026-10-16 08:00', '2026-10-16 19:00');

    $approved = AttendanceHelper::worked($employee, '2026-10-15 08:00', '2026-10-15 19:00');

    $approved->update(['overtime_status' => OvertimeStatus::APPROVED]);

    $noOvertime = AttendanceHelper::worked($employee, '2026-10-14 08:00', '2026-10-14 17:00');

    Livewire::test(ListOvertimeApprovals::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$toApprove])
        ->assertCanNotSeeTableRecords([$approved, $noOvertime]);
});

it('approves overtime in bulk', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    $first = AttendanceHelper::worked($employee, '2026-10-16 08:00', '2026-10-16 19:00');

    $second = AttendanceHelper::worked($employee, '2026-10-15 08:00', '2026-10-15 18:00');

    Livewire::test(ListOvertimeApprovals::class)
        ->selectTableRecords([$first->getKey(), $second->getKey()])
        ->callAction(TestAction::make('approve')->table()->bulk())
        ->assertNotified();

    expect($first->refresh()->overtime_status)->toBe(OvertimeStatus::APPROVED)
        ->and($second->refresh()->overtime_status)->toBe(OvertimeStatus::APPROVED);
});

it('creates a manual overtime adjustment', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    Livewire::test(ListOvertimes::class)
        ->callAction('create', data: [
            'employee_id' => $employee->id,
            'date'        => '2026-10-16',
            'duration'    => -2,
        ])
        ->assertHasNoActionErrors();

    $overtime = Overtime::where('employee_id', $employee->id)->sole();

    expect($overtime->adjustment)->toBeTrue()
        ->and($overtime->duration)->toEqualWithDelta(-2.0, 0.001)
        ->and($overtime->company_id)->toBe($employee->company_id);
});

it('does not allow editing or deleting computed overtime rows', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    AttendanceHelper::worked($employee, '2026-10-16 08:00', '2026-10-16 19:00');

    $computed = Overtime::where('employee_id', $employee->id)->sole();

    Livewire::test(ListOvertimes::class)
        ->assertCanSeeTableRecords([$computed])
        ->assertActionHidden(TestAction::make('edit')->table($computed))
        ->assertActionHidden(TestAction::make('delete')->table($computed));
});
