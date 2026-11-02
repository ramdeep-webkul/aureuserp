<?php

namespace Webkul\Attendance\Filament\Clusters\Management\Resources\OvertimeApprovalResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Webkul\Attendance\Filament\Actions\ApproveOvertimeAction;
use Webkul\Attendance\Filament\Actions\RefuseOvertimeAction;
use Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Services\AttendanceWorkflow;

class OvertimeApprovalsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.name')
                    ->label(__('attendances::filament/clusters/management/resources/overtime-approval.table.columns.employee'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('check_in')
                    ->label(__('attendances::filament/clusters/management/resources/overtime-approval.table.columns.check-in'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('check_out')
                    ->label(__('attendances::filament/clusters/management/resources/overtime-approval.table.columns.check-out'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('worked_hours')
                    ->label(__('attendances::filament/clusters/management/resources/overtime-approval.table.columns.worked-hours'))
                    ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state)),
                TextColumn::make('overtime_hours')
                    ->label(__('attendances::filament/clusters/management/resources/overtime-approval.table.columns.overtime-hours'))
                    ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state))
                    ->sortable()
                    ->summarize(
                        Sum::make()
                            ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state))
                    ),
                TextColumn::make('validated_overtime_hours')
                    ->label(__('attendances::filament/clusters/management/resources/overtime-approval.table.columns.validated-overtime-hours'))
                    ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state))
                    ->sortable()
                    ->summarize(
                        Sum::make()
                            ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state))
                    ),
                TextColumn::make('overtime_status')
                    ->label(__('attendances::filament/clusters/management/resources/overtime-approval.table.columns.overtime-status'))
                    ->badge()
                    ->sortable(),
            ])
            ->defaultSort('check_in', 'desc')
            ->filters([
                SelectFilter::make('employee_id')
                    ->label(__('attendances::filament/clusters/management/resources/overtime-approval.table.filters.employee'))
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
            ])
            ->groups([
                Group::make('employee.name')
                    ->label(__('attendances::filament/clusters/management/resources/overtime-approval.table.groups.employee'))
                    ->collapsible(),
                Group::make('overtime_status')
                    ->label(__('attendances::filament/clusters/management/resources/overtime-approval.table.groups.overtime-status'))
                    ->collapsible(),
            ])
            ->recordUrl(fn (Attendance $record) => AttendanceResource::getUrl('view', ['record' => $record]))
            ->recordActions([
                ApproveOvertimeAction::make(),
                RefuseOvertimeAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('approve')
                        ->label(__('attendances::filament/clusters/management/resources/overtime-approval.table.bulk-actions.approve.label'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records, Action $action) => static::process($records, $action, 'approveOvertime')),
                    BulkAction::make('refuse')
                        ->label(__('attendances::filament/clusters/management/resources/overtime-approval.table.bulk-actions.refuse.label'))
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records, Action $action) => static::process($records, $action, 'refuseOvertime')),
                ]),
            ]);
    }

    protected static function process(Collection $records, Action $action, string $method): void
    {
        $skipped = 0;

        foreach ($records as $record) {
            if (! Auth::user()?->can('approve', $record)) {
                $skipped++;

                continue;
            }

            app(AttendanceWorkflow::class)->{$method}($record);
        }

        Notification::make()
            ->success()
            ->title(__('attendances::filament/clusters/management/resources/overtime-approval.table.bulk-actions.notification.title'))
            ->body(__('attendances::filament/clusters/management/resources/overtime-approval.table.bulk-actions.notification.body', [
                'count'   => $records->count() - $skipped,
                'skipped' => $skipped,
            ]))
            ->send();
    }
}
