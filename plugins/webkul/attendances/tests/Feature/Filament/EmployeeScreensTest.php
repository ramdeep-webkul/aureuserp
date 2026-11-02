<?php

require_once __DIR__.'/../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/AttendanceHelper.php';

use Illuminate\Support\Facades\URL;
use Livewire\Livewire;
use Webkul\Attendance\Services\AttendanceWorkflow;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ListEmployees;
use Webkul\Employee\Filament\Resources\EmployeeResource\Pages\ViewEmployee;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('attendances');

    SecurityHelper::disableUserEvents();

    URL::resolveMissingNamedRoutesUsing(fn () => '#');

    $this->travelTo('2026-10-17 12:00');

    AttendanceHelper::settings(['display_overtime' => true]);

    FilamentHelper::actingAs([
        'view_any_employee_employee',
        'view_employee_employee',
    ]);
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('shows the presence badge on the employee list', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    app(AttendanceWorkflow::class)->checkIn($employee);

    Livewire::test(ListEmployees::class)
        ->assertOk()
        ->assertSee(__('attendances::filament/employee.presence.present'));
});

it('shows presence and overtime balance on the employee page', function () {
    $employee = AttendanceHelper::scheduledEmployee();

    AttendanceHelper::worked($employee, '2026-10-16 08:00', '2026-10-16 19:00');

    Livewire::test(ViewEmployee::class, ['record' => $employee->getRouteKey()])
        ->assertOk()
        ->assertSee(__('attendances::filament/employee.presence.absent'))
        ->assertSee('02:00');
});
