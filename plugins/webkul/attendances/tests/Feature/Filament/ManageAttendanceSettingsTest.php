<?php

require_once __DIR__.'/../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';

use BezhanSalleh\FilamentShield\Facades\FilamentShield;
use Livewire\Livewire;
use Webkul\Attendance\Enums\OvertimeValidation;
use Webkul\Attendance\Filament\Clusters\Settings\Pages\ManageAttendance;
use Webkul\Attendance\Settings\AttendanceSettings;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('attendances');

    SecurityHelper::disableUserEvents();

    $permission = array_key_first(FilamentShield::getPages()[ManageAttendance::class]['permissions'] ?? []);

    FilamentHelper::actingAs(array_filter([$permission]));
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('renders the attendance settings page', function () {
    Livewire::test(ManageAttendance::class)
        ->assertOk()
        ->assertFormFieldExists('overtime_validation');
});

it('saves the attendance settings', function () {
    Livewire::test(ManageAttendance::class)
        ->fillForm([
            'auto_check_out'             => true,
            'auto_check_out_tolerance'   => 3,
            'overtime_company_threshold' => 15,
            'overtime_validation'        => OvertimeValidation::BY_MANAGER->value,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $settings = app(AttendanceSettings::class);

    $settings->refresh();

    expect($settings->auto_check_out)->toBeTrue()
        ->and($settings->auto_check_out_tolerance)->toBe(3.0)
        ->and($settings->overtime_company_threshold)->toBe(15)
        ->and($settings->overtime_validation)->toBe(OvertimeValidation::BY_MANAGER);
});
