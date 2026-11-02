<?php

namespace Webkul\Attendance\Filament\Clusters;

use Filament\Clusters\Cluster;
use Filament\Panel;
use Webkul\Support\Enums\NavigationGroup;

class Overview extends Cluster
{
    protected static ?int $navigationSort = 1;

    public static function getSlug(?Panel $panel = null): string
    {
        return 'attendances/overview';
    }

    public static function getNavigationLabel(): string
    {
        return __('attendances::filament/clusters/overview.navigation.title');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Attendance;
    }
}
