<?php

require_once __DIR__.'/../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../Helpers/AttendanceHelper.php';

use Illuminate\Support\Facades\Schema;
use Webkul\Attendance\Enums\AttendanceMode;
use Webkul\Attendance\Enums\OvertimeStatus;
use Webkul\Attendance\Enums\OvertimeValidation;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\Overtime;
use Webkul\Attendance\Settings\AttendanceSettings;
use Webkul\PluginManager\Package;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\Support\Settings\CompanyAwareSettingsRepository;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('attendances');

    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('installs the plugin with its tables', function () {
    expect(Package::isPluginInstalled('attendances'))->toBeTrue()
        ->and(Schema::hasTable('attendances_attendances'))->toBeTrue()
        ->and(Schema::hasTable('attendances_overtimes'))->toBeTrue();
});

it('builds an attendance from its factory with typed casts', function () {
    $attendance = AttendanceHelper::attendance([
        'worked_hours'    => 8,
        'overtime_status' => OvertimeStatus::TO_APPROVE,
    ]);

    $attendance->refresh();

    expect($attendance->in_mode)->toBe(AttendanceMode::MANUAL)
        ->and($attendance->overtime_status)->toBe(OvertimeStatus::TO_APPROVE)
        ->and($attendance->worked_hours)->toBeFloat()->toBe(8.0)
        ->and($attendance->overtime_hours)->toBe(0.0)
        ->and($attendance->company_id)->not->toBeNull()
        ->and($attendance->employee)->not->toBeNull();
});

it('builds an open attendance without a check-out', function () {
    $attendance = Attendance::factory()->open()->create();

    expect($attendance->check_out)->toBeNull()
        ->and($attendance->out_mode)->toBeNull();
});

it('soft deletes attendances', function () {
    $attendance = AttendanceHelper::attendance();

    $attendance->delete();

    expect(Attendance::find($attendance->id))->toBeNull()
        ->and(Attendance::withTrashed()->find($attendance->id))->not->toBeNull();
});

it('builds an overtime ledger row from its factory', function () {
    $overtime = Overtime::factory()->create(['duration' => 1.5]);

    $overtime->refresh();

    expect($overtime->duration)->toBe(1.5)
        ->and($overtime->adjustment)->toBeFalse()
        ->and($overtime->company_id)->not->toBeNull();
});

it('loads the default settings', function () {
    $settings = app(AttendanceSettings::class);

    expect($settings->enable_top_bar)->toBeTrue()
        ->and($settings->auto_check_out)->toBeFalse()
        ->and($settings->auto_check_out_tolerance)->toBe(2.0)
        ->and($settings->absence_management)->toBeFalse()
        ->and($settings->overtime_company_threshold)->toBe(0)
        ->and($settings->overtime_employee_threshold)->toBe(0)
        ->and($settings->display_overtime)->toBeTrue()
        ->and($settings->overtime_validation)->toBe(OvertimeValidation::NO_VALIDATION)
        ->and($settings->overtime_start_date)->toBeNull();
});

it('stores the settings per company', function () {
    expect(CompanyAwareSettingsRepository::COMPANY_SCOPED_GROUPS)->toContain(AttendanceSettings::group());
});

it('registers the attendance navigation group', function () {
    expect(NavigationGroup::Attendance->getIcon())->toBe('icon-attendances')
        ->and(NavigationGroup::Attendance->getLabel())->toBe('Attendance');
});
