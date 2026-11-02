<?php

require_once __DIR__.'/../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/AttendanceHelper.php';

use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Attendance\Enums\OvertimeValidation;
use Webkul\Attendance\Filament\Clusters\Reporting\Resources\AttendanceReportResource;
use Webkul\Attendance\Filament\Clusters\Reporting\Resources\AttendanceReportResource\Pages\ListAttendanceReports;
use Webkul\Attendance\Filament\Widgets\OvertimeChart;
use Webkul\Attendance\Filament\Widgets\WorkedHoursChart;
use Webkul\Attendance\Services\AttendanceWorkflow;
use Webkul\Employee\Models\Department;

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
        'widget_attendance_worked_hours_chart',
        'widget_attendance_overtime_chart',
    ]);
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function attendanceReportChartData(string $widget): array
{
    return (fn () => $this->getData())->call(new $widget);
}

it('lists only closed sessions with worked hours totals', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    $first = AttendanceHelper::worked($employee, '2026-10-15 08:00', '2026-10-15 19:00');

    $second = AttendanceHelper::worked($employee, '2026-10-16 08:00', '2026-10-16 17:00');

    $this->travelTo('2026-10-17 13:00');

    $open = app(AttendanceWorkflow::class)->checkIn(AttendanceHelper::scheduledEmployee(attributes: ['name' => 'Open Employee']));

    Livewire::test(ListAttendanceReports::class)
        ->assertOk()
        ->assertCanSeeTableRecords([$first, $second])
        ->assertCanNotSeeTableRecords([$open])
        ->assertSee('18:00');
});

it('filters the report by period and department', function () {
    $department = Department::create(['name' => 'Support']);

    $inDepartment = AttendanceHelper::scheduledEmployee(attributes: ['department_id' => $department->id]);

    $early = AttendanceHelper::worked($inDepartment, '2026-10-05 08:00', '2026-10-05 17:00');

    $late = AttendanceHelper::worked($inDepartment, '2026-10-16 08:00', '2026-10-16 17:00');

    $other = AttendanceHelper::worked(AttendanceHelper::scheduledEmployee(), '2026-10-16 08:00', '2026-10-16 17:00');

    Livewire::test(ListAttendanceReports::class)
        ->filterTable('check_in', ['from' => '2026-10-10', 'until' => '2026-10-17'])
        ->assertCanSeeTableRecords([$late, $other])
        ->assertCanNotSeeTableRecords([$early])
        ->filterTable('department', [$department->id])
        ->assertCanSeeTableRecords([$late])
        ->assertCanNotSeeTableRecords([$early, $other]);
});

it('charts worked hours per week per employee', function () {
    $alice = AttendanceHelper::scheduledEmployee(attributes: ['name' => 'Alice']);

    $bob = AttendanceHelper::scheduledEmployee(attributes: ['name' => 'Bob']);

    AttendanceHelper::worked($alice, '2026-10-15 08:00', '2026-10-15 19:00');
    AttendanceHelper::worked($alice, '2026-10-16 08:00', '2026-10-16 17:00');
    AttendanceHelper::worked($bob, '2026-10-05 08:00', '2026-10-05 17:00');

    $data = attendanceReportChartData(WorkedHoursChart::class);

    $datasets = collect($data['datasets'])->keyBy('label');

    expect($data['labels'])->toHaveCount(8)
        ->and(end($data['labels']))->toBe('12 Oct')
        ->and($datasets['Alice']['data'][7])->toEqualWithDelta(18.0, 0.01)
        ->and($datasets['Bob']['data'][6])->toEqualWithDelta(8.0, 0.01)
        ->and($datasets['Bob']['data'][7])->toEqual(0);
});

it('groups employees beyond the top ones into others', function () {
    foreach (range(1, 9) as $index) {
        AttendanceHelper::worked(
            AttendanceHelper::scheduledEmployee(attributes: ['name' => "Employee {$index}"]),
            '2026-10-16 08:00',
            '2026-10-16 '.(8 + $index).':00',
        );
    }

    $datasets = collect(attendanceReportChartData(WorkedHoursChart::class)['datasets'])->keyBy('label');

    expect($datasets)->toHaveCount(9)
        ->and($datasets->has('Employee 1'))->toBeFalse()
        ->and($datasets[__('attendances::filament/widgets/worked-hours-chart.others')]['data'][7])->toEqualWithDelta(1.0, 0.01);
});

it('charts extra and missing hours per month from the overtime ledger', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    AttendanceHelper::worked($employee, '2026-10-15 08:00', '2026-10-15 19:00');
    AttendanceHelper::worked($employee, '2026-10-14 08:00', '2026-10-14 15:00');
    AttendanceHelper::worked($employee, '2026-09-14 08:00', '2026-09-14 18:00');

    $data = attendanceReportChartData(OvertimeChart::class);

    expect($data['labels'])->toHaveCount(12)
        ->and($data['datasets'][0]['data'][11])->toEqualWithDelta(2.0, 0.01)
        ->and($data['datasets'][1]['data'][11])->toEqualWithDelta(-2.0, 0.01)
        ->and($data['datasets'][0]['data'][10])->toEqualWithDelta(1.0, 0.01)
        ->and($data['datasets'][1]['data'][10])->toEqual(0);
});

it('shows the charts only with their widget permission', function () {
    $this->user->forceFill(['is_active' => true])->save();

    $this->get(AttendanceReportResource::getUrl('index'))
        ->assertOk()
        ->assertSee(__('attendances::filament/widgets/worked-hours-chart.heading'))
        ->assertSee(__('attendances::filament/widgets/overtime-chart.heading'));

    FilamentHelper::actingAs(['view_any_attendance_attendance'])->forceFill(['is_active' => true])->save();

    expect(WorkedHoursChart::canView())->toBeFalse()
        ->and(OvertimeChart::canView())->toBeFalse();

    FilamentHelper::actingAs([])->forceFill(['is_active' => true])->save();

    $this->get(AttendanceReportResource::getUrl('index'))->assertForbidden();
});
