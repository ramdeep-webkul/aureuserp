<?php

namespace Webkul\Attendance\Filament\Clusters\Management\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Attendance\Filament\Clusters\Management;
use Webkul\Attendance\Filament\Clusters\Management\Resources\OvertimeApprovalResource\Pages\ListOvertimeApprovals;
use Webkul\Attendance\Filament\Clusters\Management\Resources\OvertimeApprovalResource\Tables\OvertimeApprovalsTable;
use Webkul\Attendance\Models\Attendance;

class OvertimeApprovalResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static ?string $slug = 'overtime-approvals';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-check-badge';

    protected static ?string $cluster = Management::class;

    protected static ?int $navigationSort = 1;

    protected static bool $isGloballySearchable = false;

    public static function getModelLabel(): string
    {
        return __('attendances::filament/clusters/management/resources/overtime-approval.model-label');
    }

    public static function getNavigationLabel(): string
    {
        return __('attendances::filament/clusters/management/resources/overtime-approval.navigation.title');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->whereNotNull('check_out')
            ->where(fn (Builder $query) => $query
                ->where('overtime_hours', '>', 0.005)
                ->orWhere('overtime_hours', '<', -0.005));
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function table(Table $table): Table
    {
        return OvertimeApprovalsTable::configure($table);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOvertimeApprovals::route('/'),
        ];
    }
}
