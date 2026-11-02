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
use Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource;
use Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource\Pages\CreateAttendance;
use Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource\Pages\EditAttendance;
use Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource\Pages\ListAttendances;
use Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource\Pages\ViewAttendance;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\Overtime;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('attendances');

    SecurityHelper::disableUserEvents();

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    $this->travelTo('2026-10-17 12:00');

    AttendanceHelper::settings([
        'overtime_validation'         => OvertimeValidation::NO_VALIDATION,
        'overtime_company_threshold'  => 0,
        'overtime_employee_threshold' => 0,
        'overtime_start_date'         => null,
    ]);

    $this->user = FilamentHelper::actingAs([
        'view_any_attendance_attendance',
        'view_attendance_attendance',
        'create_attendance_attendance',
        'update_attendance_attendance',
        'delete_attendance_attendance',
        'delete_any_attendance_attendance',
        'restore_attendance_attendance',
        'restore_any_attendance_attendance',
    ]);
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('lists attendances', function () {
    $attendance = AttendanceHelper::worked(AttendanceHelper::scheduledEmployee(), '2026-10-16 08:00', '2026-10-16 17:00');

    Livewire::test(ListAttendances::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$attendance]);
});

it('creates an attendance through the workflow', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    Livewire::test(CreateAttendance::class)
        ->fillForm([
            'employee_id' => $employee->id,
            'check_in'    => '2026-10-16 08:00:00',
            'check_out'   => '2026-10-16 19:00:00',
        ])
        ->call('create')
        ->assertHasNoFormErrors();

    $attendance = Attendance::where('employee_id', $employee->id)->sole();

    expect($attendance->worked_hours)->toEqualWithDelta(10.0, 0.001)
        ->and($attendance->overtime_hours)->toEqualWithDelta(2.0, 0.001)
        ->and($attendance->creator_id)->toBe($this->user->id);
});

it('does not create an overlapping attendance', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    AttendanceHelper::worked($employee, '2026-10-16 08:00', '2026-10-16 17:00');

    Livewire::test(CreateAttendance::class)
        ->fillForm([
            'employee_id' => $employee->id,
            'check_in'    => '2026-10-16 10:00:00',
            'check_out'   => '2026-10-16 11:00:00',
        ])
        ->call('create')
        ->assertNotified();

    expect(Attendance::where('employee_id', $employee->id)->count())->toBe(1);
});

it('requires a reason when an attendance is edited', function () {
    $attendance = AttendanceHelper::worked(AttendanceHelper::scheduledEmployee(), '2026-10-16 08:00', '2026-10-16 17:00');

    Livewire::test(EditAttendance::class, ['record' => $attendance->getRouteKey()])
        ->assertFormSet(['edit_reason' => null])
        ->fillForm(['check_out' => '2026-10-16 18:00:00'])
        ->call('save')
        ->assertHasFormErrors(['edit_reason' => 'required']);
});

it('corrects an attendance and recomputes its hours', function () {
    $attendance = AttendanceHelper::worked(AttendanceHelper::scheduledEmployee(), '2026-10-16 08:00', '2026-10-16 17:00');

    Livewire::test(EditAttendance::class, ['record' => $attendance->getRouteKey()])
        ->fillForm([
            'check_out'   => '2026-10-16 18:00:00',
            'edit_reason' => 'Forgot to check out',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $attendance->refresh();

    expect($attendance->edit_reason)->toBe('Forgot to check out')
        ->and($attendance->worked_hours)->toEqualWithDelta(9.0, 0.001)
        ->and($attendance->overtime_hours)->toEqualWithDelta(1.0, 0.001);
});

it('deletes and restores an attendance from the table', function () {
    $attendance = AttendanceHelper::worked(AttendanceHelper::scheduledEmployee(), '2026-10-16 08:00', '2026-10-16 19:00');

    Livewire::test(ListAttendances::class)
        ->callAction(TestAction::make('delete')->table($attendance));

    expect($attendance->refresh()->trashed())->toBeTrue()
        ->and(attendanceOvertimeRows($attendance))->toBe(0);

    Livewire::test(ListAttendances::class)
        ->set('activeTableView', 'archived')
        ->callAction(TestAction::make('restore')->table($attendance));

    expect($attendance->refresh()->trashed())->toBeFalse()
        ->and(attendanceOvertimeRows($attendance))->toBe(1);
});

it('approves overtime with edited validated hours from the view page', function () {
    AttendanceHelper::settings(['overtime_validation' => OvertimeValidation::BY_MANAGER]);

    $attendance = AttendanceHelper::worked(AttendanceHelper::scheduledEmployee(), '2026-10-16 08:00', '2026-10-16 19:00');

    expect($attendance->overtime_status)->toBe(OvertimeStatus::TO_APPROVE);

    Livewire::test(ViewAttendance::class, ['record' => $attendance->getRouteKey()])
        ->callAction('approveOvertime', data: ['validated_overtime_hours' => 1.5])
        ->assertHasNoActionErrors();

    $attendance->refresh();

    expect($attendance->overtime_status)->toBe(OvertimeStatus::APPROVED)
        ->and($attendance->validated_overtime_hours)->toEqualWithDelta(1.5, 0.001);
});

it('refuses overtime from the table', function () {
    AttendanceHelper::settings(['overtime_validation' => OvertimeValidation::BY_MANAGER]);

    $attendance = AttendanceHelper::worked(AttendanceHelper::scheduledEmployee(), '2026-10-16 08:00', '2026-10-16 19:00');

    Livewire::test(ListAttendances::class)
        ->callAction(TestAction::make('refuseOvertime')->table($attendance));

    $attendance->refresh();

    expect($attendance->overtime_status)->toBe(OvertimeStatus::REFUSED)
        ->and($attendance->validated_overtime_hours)->toEqualWithDelta(0.0, 0.001);
});

it('limits approval to the attendance manager or global users', function () {
    $attendance = AttendanceHelper::worked(AttendanceHelper::scheduledEmployee(), '2026-10-16 08:00', '2026-10-16 19:00');

    $user = FilamentHelper::actingAs(['update_attendance_attendance'], global: false);

    expect($user->can('approve', $attendance))->toBeFalse();

    $attendance->employee->update(['attendance_manager_id' => $user->id]);

    expect($user->can('approve', $attendance->refresh()))->toBeTrue();
});

it('limits updates to own, managed or created attendances for individual users', function () {
    $attendance = AttendanceHelper::worked(AttendanceHelper::scheduledEmployee(), '2026-10-16 08:00', '2026-10-16 17:00');

    $user = FilamentHelper::actingAs(['update_attendance_attendance'], global: false);

    expect($user->can('update', $attendance))->toBeFalse();

    $attendance->employee->update(['user_id' => $user->id]);

    expect($user->can('update', $attendance->refresh()))->toBeTrue();
});

it('formats hours as hours and minutes', function () {
    expect(AttendanceResource::formatHours(1.5))->toBe('01:30')
        ->and(AttendanceResource::formatHours(-0.25))->toBe('-00:15')
        ->and(AttendanceResource::formatHours(0.0))->toBe('00:00');
});

function attendanceOvertimeRows(Attendance $attendance): int
{
    return Overtime::where('employee_id', $attendance->employee_id)->count();
}
