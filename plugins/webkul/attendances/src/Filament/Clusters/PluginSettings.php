<?php

namespace Webkul\Attendance\Filament\Clusters;

use Filament\Clusters\Cluster;
use Webkul\Support\Enums\NavigationGroup;

class PluginSettings extends Cluster
{
    protected static ?string $slug = 'attendances/settings';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return __('attendances::app.navigation.settings.label');
    }

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Attendance;
    }
}
