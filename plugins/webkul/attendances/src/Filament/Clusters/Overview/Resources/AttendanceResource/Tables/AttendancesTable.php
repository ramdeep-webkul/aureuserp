<?php

namespace Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource\Tables;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ExportAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Webkul\Attendance\Enums\AttendanceMode;
use Webkul\Attendance\Enums\OvertimeStatus;
use Webkul\Attendance\Exceptions\AttendanceValidationException;
use Webkul\Attendance\Filament\Actions\ApproveOvertimeAction;
use Webkul\Attendance\Filament\Actions\RefuseOvertimeAction;
use Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource;
use Webkul\Attendance\Filament\Exports\AttendanceExporter;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Services\AttendanceWorkflow;
use Webkul\Chatter\Filament\Actions\ActivityTableAction;

class AttendancesTable
{
    public static function configure(Table $table, array $customColumns = []): Table
    {
        return $table
            ->columns(array_merge([
                TextColumn::make('employee.name')
                    ->label(__('attendances::filament/clusters/overview/resources/attendance.table.columns.employee'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('check_in')
                    ->label(__('attendances::filament/clusters/overview/resources/attendance.table.columns.check-in'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('check_out')
                    ->label(__('attendances::filament/clusters/overview/resources/attendance.table.columns.check-out'))
                    ->dateTime()
                    ->placeholder('—')
                    ->sortable(),
                TextColumn::make('worked_hours')
                    ->label(__('attendances::filament/clusters/overview/resources/attendance.table.columns.worked-hours'))
                    ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state))
                    ->sortable()
                    ->summarize(
                        Sum::make()
                            ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state))
                    ),
                TextColumn::make('overtime_hours')
                    ->label(__('attendances::filament/clusters/overview/resources/attendance.table.columns.overtime-hours'))
                    ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state))
                    ->sortable()
                    ->toggleable()
                    ->summarize(
                        Sum::make()
                            ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state))
                    ),
                TextColumn::make('overtime_status')
                    ->label(__('attendances::filament/clusters/overview/resources/attendance.table.columns.overtime-status'))
                    ->badge()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('in_mode')
                    ->label(__('attendances::filament/clusters/overview/resources/attendance.table.columns.in-mode'))
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('out_mode')
                    ->label(__('attendances::filament/clusters/overview/resources/attendance.table.columns.out-mode'))
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('edit_reason')
                    ->label(__('attendances::filament/clusters/overview/resources/attendance.table.columns.edit-reason'))
                    ->placeholder('—')
                    ->limit(40)
                    ->toggleable(isToggledHiddenByDefault: true),
            ], $customColumns))
            ->defaultSort('check_in', 'desc')
            ->filters([
                SelectFilter::make('employee_id')
                    ->label(__('attendances::filament/clusters/overview/resources/attendance.table.filters.employee'))
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                SelectFilter::make('overtime_status')
                    ->label(__('attendances::filament/clusters/overview/resources/attendance.table.filters.overtime-status'))
                    ->options(OvertimeStatus::options()),
                SelectFilter::make('in_mode')
                    ->label(__('attendances::filament/clusters/overview/resources/attendance.table.filters.in-mode'))
                    ->options(AttendanceMode::options()),
                SelectFilter::make('out_mode')
                    ->label(__('attendances::filament/clusters/overview/resources/attendance.table.filters.out-mode'))
                    ->options(AttendanceMode::options()),
            ])
            ->groups([
                Group::make('employee.name')
                    ->label(__('attendances::filament/clusters/overview/resources/attendance.table.groups.employee'))
                    ->collapsible(),
                Group::make('employee.department.name')
                    ->label(__('attendances::filament/clusters/overview/resources/attendance.table.groups.department'))
                    ->collapsible(),
                Group::make('employee.attendanceManager.name')
                    ->label(__('attendances::filament/clusters/overview/resources/attendance.table.groups.manager'))
                    ->collapsible(),
                Group::make('in_mode')
                    ->label(__('attendances::filament/clusters/overview/resources/attendance.table.groups.method'))
                    ->collapsible(),
                Group::make('check_in')
                    ->label(__('attendances::filament/clusters/overview/resources/attendance.table.groups.check-in'))
                    ->date()
                    ->collapsible(),
            ])
            ->recordActions([
                ActivityTableAction::make()
                    ->hidden(fn (Attendance $record) => $record->trashed()),
                ActionGroup::make([
                    ViewAction::make()
                        ->hidden(fn (Attendance $record) => $record->trashed()),
                    EditAction::make()
                        ->hidden(fn (Attendance $record) => $record->trashed()),
                    ApproveOvertimeAction::make(),
                    RefuseOvertimeAction::make(),
                    RestoreAction::make()
                        ->action(function (Attendance $record, Action $action) {
                            try {
                                app(AttendanceWorkflow::class)->restore($record);
                            } catch (AttendanceValidationException $e) {
                                Notification::make()
                                    ->danger()
                                    ->title($e->getMessage())
                                    ->send();

                                $action->halt();
                            }

                            $action->success();
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('attendances::filament/clusters/overview/resources/attendance.table.actions.restore.notification.title'))
                                ->body(__('attendances::filament/clusters/overview/resources/attendance.table.actions.restore.notification.body'))
                        ),
                    DeleteAction::make()
                        ->using(fn (Attendance $record) => app(AttendanceWorkflow::class)->delete($record))
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('attendances::filament/clusters/overview/resources/attendance.table.actions.delete.notification.title'))
                                ->body(__('attendances::filament/clusters/overview/resources/attendance.table.actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    RestoreBulkAction::make()
                        ->action(function (Collection $records, Action $action) {
                            $errors = [];

                            foreach ($records as $record) {
                                try {
                                    app(AttendanceWorkflow::class)->restore($record);
                                } catch (AttendanceValidationException $e) {
                                    $errors[] = $e->getMessage();
                                }
                            }

                            if ($errors) {
                                Notification::make()
                                    ->warning()
                                    ->title(__('attendances::filament/clusters/overview/resources/attendance.table.bulk-actions.restore.partial'))
                                    ->body(implode("\n", array_unique($errors)))
                                    ->send();

                                return;
                            }

                            $action->success();
                        })
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('attendances::filament/clusters/overview/resources/attendance.table.bulk-actions.restore.notification.title'))
                                ->body(__('attendances::filament/clusters/overview/resources/attendance.table.bulk-actions.restore.notification.body'))
                        ),
                    DeleteBulkAction::make()
                        ->using(fn (Collection $records) => $records->each(fn (Attendance $record) => app(AttendanceWorkflow::class)->delete($record)))
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('attendances::filament/clusters/overview/resources/attendance.table.bulk-actions.delete.notification.title'))
                                ->body(__('attendances::filament/clusters/overview/resources/attendance.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
                ExportAction::make()
                    ->label(__('attendances::filament/clusters/overview/resources/attendance.table.toolbar-actions.export.label'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(AttendanceExporter::class),
            ]);
    }
}
