<?php

namespace Webkul\Attendance\Filament\Clusters\Management\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Webkul\Attendance\Filament\Clusters\Management;
use Webkul\Attendance\Filament\Clusters\Management\Resources\OvertimeResource\Pages\ListOvertimes;
use Webkul\Attendance\Filament\Clusters\Management\Resources\OvertimeResource\Schemas\OvertimeForm;
use Webkul\Attendance\Filament\Clusters\Management\Resources\OvertimeResource\Tables\OvertimesTable;
use Webkul\Attendance\Models\Overtime;

class OvertimeResource extends Resource
{
    protected static ?string $model = Overtime::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static ?string $cluster = Management::class;

    protected static ?int $navigationSort = 2;

    protected static bool $isGloballySearchable = false;

    public static function getModelLabel(): string
    {
        return __('attendances::filament/clusters/management/resources/overtime.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('attendances::filament/clusters/management/resources/overtime.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return OvertimeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return OvertimesTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOvertimes::route('/'),
        ];
    }
}
