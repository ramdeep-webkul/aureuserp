<?php

namespace Webkul\Attendance\Filament\Clusters\Overview\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Attendance\Filament\Clusters\Overview;
use Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource\Pages\CreateAttendance;
use Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource\Pages\EditAttendance;
use Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource\Pages\ListAttendances;
use Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource\Pages\ViewAttendance;
use Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource\Schemas\AttendanceForm;
use Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource\Schemas\AttendanceInfolist;
use Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource\Tables\AttendancesTable;
use Webkul\Attendance\Models\Attendance;
use Webkul\Field\Filament\Traits\HasCustomFields;

class AttendanceResource extends Resource
{
    use HasCustomFields;

    protected static ?string $model = Attendance::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-finger-print';

    protected static ?string $cluster = Overview::class;

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'employee.name';

    public static function getModelLabel(): string
    {
        return __('attendances::filament/clusters/overview/resources/attendance.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('attendances::filament/clusters/overview/resources/attendance.navigation.title');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['employee.name'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('attendances::filament/clusters/overview/resources/attendance.global-search.check-in')  => $record->check_in?->format('Y-m-d H:i') ?? '—',
            __('attendances::filament/clusters/overview/resources/attendance.global-search.check-out') => $record->check_out?->format('Y-m-d H:i') ?? '—',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return AttendanceForm::configure($schema, static::getCustomFormFields());
    }

    public static function table(Table $table): Table
    {
        return AttendancesTable::configure($table, static::getCustomTableColumns());
    }

    public static function infolist(Schema $schema): Schema
    {
        return AttendanceInfolist::configure($schema, static::getCustomInfolistEntries());
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListAttendances::route('/'),
            'create' => CreateAttendance::route('/create'),
            'view'   => ViewAttendance::route('/{record}'),
            'edit'   => EditAttendance::route('/{record}/edit'),
        ];
    }

    public static function formatHours(?float $hours): string
    {
        $minutes = (int) round(abs((float) $hours) * 60);

        return ($hours < 0 && $minutes > 0 ? '-' : '').sprintf('%02d:%02d', intdiv($minutes, 60), $minutes % 60);
    }
}
