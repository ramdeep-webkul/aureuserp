<?php

namespace Webkul\Attendance\Filament\Clusters\Management\Resources\OvertimeResource\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Webkul\Attendance\Filament\Clusters\Management\Resources\OvertimeResource;
use Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource;

class OvertimesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.name')
                    ->label(__('attendances::filament/clusters/management/resources/overtime.table.columns.employee'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->label(__('attendances::filament/clusters/management/resources/overtime.table.columns.date'))
                    ->date()
                    ->sortable(),
                TextColumn::make('duration')
                    ->label(__('attendances::filament/clusters/management/resources/overtime.table.columns.duration'))
                    ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state))
                    ->sortable()
                    ->summarize(
                        Sum::make()
                            ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state))
                    ),
                TextColumn::make('duration_real')
                    ->label(__('attendances::filament/clusters/management/resources/overtime.table.columns.duration-real'))
                    ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state))
                    ->toggleable(isToggledHiddenByDefault: true),
                IconColumn::make('adjustment')
                    ->label(__('attendances::filament/clusters/management/resources/overtime.table.columns.adjustment'))
                    ->boolean(),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('employee_id')
                    ->label(__('attendances::filament/clusters/management/resources/overtime.table.filters.employee'))
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                TernaryFilter::make('adjustment')
                    ->label(__('attendances::filament/clusters/management/resources/overtime.table.filters.adjustment')),
            ])
            ->groups([
                Group::make('employee.name')
                    ->label(__('attendances::filament/clusters/management/resources/overtime.table.groups.employee'))
                    ->collapsible(),
                Group::make('date')
                    ->label(__('attendances::filament/clusters/management/resources/overtime.table.groups.date'))
                    ->date()
                    ->collapsible(),
            ])
            ->recordActions([
                EditAction::make()
                    ->mutateDataUsing(fn (array $data): array => [...$data, 'duration_real' => $data['duration']])
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('attendances::filament/clusters/management/resources/overtime.table.actions.edit.notification.title'))
                            ->body(__('attendances::filament/clusters/management/resources/overtime.table.actions.edit.notification.body'))
                    ),
                DeleteAction::make()
                    ->successNotification(
                        Notification::make()
                            ->success()
                            ->title(__('attendances::filament/clusters/management/resources/overtime.table.actions.delete.notification.title'))
                            ->body(__('attendances::filament/clusters/management/resources/overtime.table.actions.delete.notification.body'))
                    ),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->successNotification(
                            Notification::make()
                                ->success()
                                ->title(__('attendances::filament/clusters/management/resources/overtime.table.bulk-actions.delete.notification.title'))
                                ->body(__('attendances::filament/clusters/management/resources/overtime.table.bulk-actions.delete.notification.body'))
                        ),
                ]),
            ])
            ->checkIfRecordIsSelectableUsing(fn (Model $record): bool => OvertimeResource::can('delete', $record));
    }
}
