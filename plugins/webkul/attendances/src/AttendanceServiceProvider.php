<?php

namespace Webkul\Attendance;

use Filament\Panel;
use Illuminate\Console\Scheduling\Schedule;
use Livewire\Livewire;
use Webkul\Attendance\Console\Commands\AbsenceDetectionCommand;
use Webkul\Attendance\Console\Commands\AutoCheckOutCommand;
use Webkul\Attendance\Livewire\CheckInOut;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Models\Overtime;
use Webkul\Attendance\Observers\EmployeeObserver;
use Webkul\Chatter\Services\ChatterCleanupService;
use Webkul\Employee\Models\Employee;
use Webkul\PluginManager\Console\Commands\InstallCommand;
use Webkul\PluginManager\Console\Commands\UninstallCommand;
use Webkul\PluginManager\Package;
use Webkul\PluginManager\PackageServiceProvider;

class AttendanceServiceProvider extends PackageServiceProvider
{
    public static string $name = 'attendances';

    public static string $viewNamespace = 'attendances';

    public function configureCustomPackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasViews()
            ->hasTranslations()
            ->hasMigrations([
                '2026_11_02_000001_create_attendances_attendances_table',
                '2026_11_02_000002_create_attendances_overtimes_table',
            ])
            ->runsMigrations()
            ->hasSettings([
                '2026_11_02_000003_create_attendances_attendance_settings',
            ])
            ->runsSettings()
            ->hasDependencies([
                'employees',
            ])
            ->hasCommands([
                AutoCheckOutCommand::class,
                AbsenceDetectionCommand::class,
            ])
            ->hasSeeder('Webkul\\Attendance\\Database\\Seeders\\DatabaseSeeder')
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->installDependencies()
                    ->runsMigrations()
                    ->runsSeeders();
            })
            ->hasUninstallCommand(function (UninstallCommand $command) {
                $command->endWith(function () {
                    ChatterCleanupService::purgeForModels([Attendance::class, Overtime::class]);
                });
            })
            ->icon('attendances');
    }

    public function packageBooted(): void
    {
        Livewire::component('attendances-check-in-out', CheckInOut::class);

        if (! Package::isPluginInstalled(static::$name)) {
            return;
        }

        Employee::observe(EmployeeObserver::class);

        $this->callAfterResolving(Schedule::class, function (Schedule $schedule): void {
            $schedule->command(AutoCheckOutCommand::class)->everyFourHours();

            $schedule->command(AbsenceDetectionCommand::class)->everyFourHours();
        });
    }

    public function packageRegistered(): void
    {
        Panel::configureUsing(function (Panel $panel): void {
            $panel->plugin(AttendancePlugin::make());
        });
    }
}
