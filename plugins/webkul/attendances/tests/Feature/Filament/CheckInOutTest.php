<?php

require_once __DIR__.'/../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/TestBootstrapHelper.php';
require_once __DIR__.'/../../../../support/tests/Helpers/FilamentHelper.php';
require_once __DIR__.'/../../Helpers/AttendanceHelper.php';

use Livewire\Livewire;
use Webkul\Attendance\Enums\AttendanceMode;
use Webkul\Attendance\Livewire\CheckInOut;
use Webkul\Attendance\Models\Attendance;

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('attendances');

    SecurityHelper::disableUserEvents();

    $this->travelTo(AttendanceHelper::at('2026-10-16 08:00'));

    AttendanceHelper::settings(['enable_top_bar' => true, 'display_overtime' => true]);

    $this->user = FilamentHelper::actingAs(['view_any_attendance_attendance']);
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

it('checks the linked employee in and out from the top bar', function () {
    $employee = AttendanceHelper::scheduledEmployee(attributes: ['user_id' => $this->user->id]);

    $component = Livewire::test(CheckInOut::class)
        ->assertSee(__('attendances::livewire/check-in-out.actions.check-in'))
        ->call('toggle')
        ->assertNotified(__('attendances::livewire/check-in-out.notifications.checked-in'))
        ->assertSee(__('attendances::livewire/check-in-out.actions.check-out'));

    $attendance = Attendance::where('employee_id', $employee->id)->sole();

    expect($attendance->in_mode)->toBe(AttendanceMode::TOP_BAR)
        ->and($attendance->check_out)->toBeNull();

    $this->travelTo(AttendanceHelper::at('2026-10-16 12:00'));

    $component->call('toggle')
        ->assertNotified(__('attendances::livewire/check-in-out.notifications.checked-out'))
        ->assertSee('04:00');

    expect($attendance->refresh()->out_mode)->toBe(AttendanceMode::TOP_BAR)
        ->and($attendance->worked_hours)->toEqualWithDelta(4.0, 0.01);
});

it('hides the button without a linked employee, permission or setting', function () {
    Livewire::test(CheckInOut::class)
        ->assertDontSee(__('attendances::livewire/check-in-out.actions.check-in'));

    AttendanceHelper::scheduledEmployee(attributes: ['user_id' => $this->user->id]);

    AttendanceHelper::settings(['enable_top_bar' => false]);

    Livewire::test(CheckInOut::class)
        ->assertDontSee(__('attendances::livewire/check-in-out.actions.check-in'));

    AttendanceHelper::settings(['enable_top_bar' => true]);

    FilamentHelper::actingAs([]);

    Livewire::test(CheckInOut::class)
        ->assertDontSee(__('attendances::livewire/check-in-out.actions.check-in'));
});
