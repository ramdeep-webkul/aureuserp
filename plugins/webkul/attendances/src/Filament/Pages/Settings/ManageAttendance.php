<?php

namespace Webkul\Attendance\Filament\Pages\Settings;

use UnitEnum;
use Webkul\Attendance\Filament\Clusters\PluginSettings;
use Webkul\Attendance\Filament\Clusters\Settings\Pages\ManageAttendance as BaseManageAttendance;

class ManageAttendance extends BaseManageAttendance
{
    protected static ?string $cluster = PluginSettings::class;

    protected static string|UnitEnum|null $navigationGroup = null;

    protected static ?string $slug = 'manage-attendance';
}
