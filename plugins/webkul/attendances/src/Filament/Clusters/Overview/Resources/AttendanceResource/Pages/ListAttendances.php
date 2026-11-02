<?php

namespace Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Attendance\Enums\AttendanceMode;
use Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;

class ListAttendances extends ListRecords
{
    use HasTableViews;

    protected static string $resource = AttendanceResource::class;

    public function getPresetTableViews(): array
    {
        return [
            'my_attendances' => PresetView::make(__('attendances::filament/clusters/overview/resources/attendance/pages/list-attendances.preset-views.my-attendances'))
                ->icon('heroicon-o-user')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('employee', fn (Builder $query) => $query->where('user_id', Auth::id()))),
            'my_team' => PresetView::make(__('attendances::filament/clusters/overview/resources/attendance/pages/list-attendances.preset-views.my-team'))
                ->icon('heroicon-o-users')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereHas('employee', fn (Builder $query) => $query->where('attendance_manager_id', Auth::id()))),
            'at_work' => PresetView::make(__('attendances::filament/clusters/overview/resources/attendance/pages/list-attendances.preset-views.at-work'))
                ->icon('heroicon-o-play-circle')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNull('check_out')),
            'errors' => PresetView::make(__('attendances::filament/clusters/overview/resources/attendance/pages/list-attendances.preset-views.errors'))
                ->icon('heroicon-o-exclamation-triangle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where(fn (Builder $query) => $query
                    ->where('worked_hours', '>', 16)
                    ->orWhere(fn (Builder $query) => $query
                        ->whereNull('check_out')
                        ->where('check_in', '<', now()->subDay())))),
            'auto_checked_out' => PresetView::make(__('attendances::filament/clusters/overview/resources/attendance/pages/list-attendances.preset-views.auto-checked-out'))
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('out_mode', AttendanceMode::AUTO_CHECK_OUT)),
            'archived' => PresetView::make(__('attendances::filament/clusters/overview/resources/attendance/pages/list-attendances.preset-views.archived'))
                ->icon('heroicon-o-archive-box')
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed()),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('attendances::filament/clusters/overview/resources/attendance/pages/list-attendances.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle'),
        ];
    }
}
