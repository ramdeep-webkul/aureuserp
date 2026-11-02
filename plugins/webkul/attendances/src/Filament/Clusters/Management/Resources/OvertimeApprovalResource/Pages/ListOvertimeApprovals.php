<?php

namespace Webkul\Attendance\Filament\Clusters\Management\Resources\OvertimeApprovalResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Attendance\Enums\OvertimeStatus;
use Webkul\Attendance\Filament\Clusters\Management\Resources\OvertimeApprovalResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListOvertimeApprovals extends ListRecords
{
    use HasTableViews;

    protected static string $resource = OvertimeApprovalResource::class;

    public function getPresetTableViews(): array
    {
        return [
            'to_approve' => PresetView::make(__('attendances::filament/clusters/management/resources/overtime-approval/pages/list-overtime-approvals.preset-views.to-approve'))
                ->icon('heroicon-o-clock')
                ->favorite()
                ->setAsDefault()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('overtime_status', OvertimeStatus::TO_APPROVE)),
            'my_team' => PresetView::make(__('attendances::filament/clusters/management/resources/overtime-approval/pages/list-overtime-approvals.preset-views.my-team'))
                ->icon('heroicon-o-users')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('employee', fn (Builder $query) => $query->where('attendance_manager_id', Auth::id()))),
            'approved' => PresetView::make(__('attendances::filament/clusters/management/resources/overtime-approval/pages/list-overtime-approvals.preset-views.approved'))
                ->icon('heroicon-o-check-badge')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('overtime_status', OvertimeStatus::APPROVED)),
            'refused' => PresetView::make(__('attendances::filament/clusters/management/resources/overtime-approval/pages/list-overtime-approvals.preset-views.refused'))
                ->icon('heroicon-o-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('overtime_status', OvertimeStatus::REFUSED)),
        ];
    }
}
