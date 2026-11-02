<?php

namespace Webkul\Attendance\Filament\Clusters\Reporting\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Attendance\Filament\Clusters\Reporting;
use Webkul\Attendance\Filament\Clusters\Reporting\Resources\AttendanceReportResource\Pages\ListAttendanceReports;
use Webkul\Attendance\Filament\Clusters\Reporting\Resources\AttendanceReportResource\Tables\AttendanceReportsTable;
use Webkul\Attendance\Models\Attendance;

class AttendanceReportResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $slug = 'attendance-reports';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $cluster = Reporting::class;

    protected static ?int $navigationSort = 1;

    protected static bool $isGloballySearchable = false;

    public static function getModelLabel(): string
    {
        return __('attendances::filament/clusters/reporting/resources/attendance-report.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('attendances::filament/clusters/reporting/resources/attendance-report.navigation.title');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->whereNotNull('check_out');
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return AttendanceReportsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListAttendanceReports::route('/'),
        ];
    }
}
