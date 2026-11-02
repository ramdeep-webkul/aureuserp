<?php

namespace Webkul\Attendance;

use Filament\Contracts\Plugin;
use Filament\Facades\Filament;
use Filament\Panel;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;
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
                    ->renderHook(
                        PanelsRenderHook::TOPBAR_END,
                        fn (): string => Filament::auth()->check()
                            ? Blade::render("@livewire('attendances-check-in-out')")
                            : '',
                    );
            });
    }

    public function boot(Panel $panel): void
    {
        //
    }
}
