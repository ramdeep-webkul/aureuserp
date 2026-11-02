<?php

namespace Webkul\Attendance;

use Filament\Actions\Action;
use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
use Webkul\Attendance\Livewire\CheckInOut;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Services\AttendanceWorkflow;
use Webkul\Attendance\Services\WorkedHoursCalculator;
use Webkul\PluginManager\Package;

class AttendancePlugin implements Plugin
{
    public function getId(): string
    {
        return 'attendances';
    }

    public static function make(): static
    {
        return app(static::class);
    }

    public function register(Panel $panel): void
    {
        if (! Package::isPluginInstalled($this->getId())) {
            return;
        }

        $panel
            ->when($panel->getId() == 'admin', function (Panel $panel) {
                $panel
                    ->discoverResources(
                        in: __DIR__.'/Filament/Resources',
                        for: 'Webkul\\Attendance\\Filament\\Resources'
                    )
                    ->discoverPages(
                        in: __DIR__.'/Filament/Pages',
                        for: 'Webkul\\Attendance\\Filament\\Pages'
                    )
                    ->discoverClusters(
                        in: __DIR__.'/Filament/Clusters',
                        for: 'Webkul\\Attendance\\Filament\\Clusters'
                    )
                    ->discoverWidgets(
                        in: __DIR__.'/Filament/Widgets',
                        for: 'Webkul\\Attendance\\Filament\\Widgets'
                    )
                    ->userMenuItems([
                        'attendances-check-in-out' => Action::make('attendances-check-in-out')
                            ->label(fn (): string => __('attendances::livewire/check-in-out.menu'))
                            ->icon('heroicon-o-clock')
                            ->badge(fn (): string => $this->checkInTime() ?? __('attendances::livewire/check-in-out.status.out'))
                            ->badgeColor(fn (): string => $this->isCheckedIn() ? 'success' : 'danger')
                            ->alpineClickHandler("\$dispatch('open-modal', { id: '".CheckInOut::MODAL_ID."' })")
                            ->visible(fn (): bool => (bool) CheckInOut::currentEmployee())
                            ->sort(PHP_INT_MAX - 1),
                    ])
                    ->renderHook(
                        PanelsRenderHook::BODY_END,
                        fn (): string => Filament::auth()->check()
                            ? Blade::render("@livewire('attendances-check-in-out')")
                            : '',
                    );
            });
    }

    protected function isCheckedIn(): bool
    {
        $employee = CheckInOut::currentEmployee();

        return $employee && Attendance::isCheckedIn($employee);
    }

    protected function checkInTime(): ?string
    {
        $employee = CheckInOut::currentEmployee();

        if (! $employee) {
            return null;
        }

        return app(AttendanceWorkflow::class)->openAttendance($employee)?->check_in
            ->copy()
            ->setTimezone(app(WorkedHoursCalculator::class)->timezone($employee))
            ->format('H:i');
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
