<?php

namespace Webkul\Attendance\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Panel;
use Webkul\Support\Enums\NavigationGroup;

class Reporting extends Cluster
{
    protected static ?int $navigationSort = 3;

    public static function getSlug(?Panel $panel = null): string
    {
        return 'attendances/reporting';
    }

    public static function getNavigationLabel(): string
    {
        return __('attendances::filament/clusters/reporting.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Attendance;
    }
}
