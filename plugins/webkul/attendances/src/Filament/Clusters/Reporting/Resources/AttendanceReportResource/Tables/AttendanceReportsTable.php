<?php

namespace Webkul\Attendance\Filament\Clusters\Reporting\Resources\AttendanceReportResource\Tables;

use Filament\Actions\ExportAction;
use Filament\Forms\Components\DatePicker;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource;
use Webkul\Attendance\Filament\Exports\AttendanceExporter;
use Webkul\Employee\Models\Department;

class AttendanceReportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('employee.name')
                    ->label(__('attendances::filament/clusters/reporting/resources/attendance-report.table.columns.employee'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('employee.department.name')
                    ->label(__('attendances::filament/clusters/reporting/resources/attendance-report.table.columns.department'))
                    ->placeholder('—')
                    ->toggleable(),
                TextColumn::make('check_in')
                    ->label(__('attendances::filament/clusters/reporting/resources/attendance-report.table.columns.check-in'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('check_out')
                    ->label(__('attendances::filament/clusters/reporting/resources/attendance-report.table.columns.check-out'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('worked_hours')
                    ->label(__('attendances::filament/clusters/reporting/resources/attendance-report.table.columns.worked-hours'))
                    ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state))
                    ->sortable()
                    ->summarize(
                        Sum::make()
                            ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state))
                    ),
                TextColumn::make('expected_hours')
                    ->label(__('attendances::filament/clusters/reporting/resources/attendance-report.table.columns.expected-hours'))
                    ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state))
                    ->sortable()
                    ->toggleable()
                    ->summarize(
                        Sum::make()
                            ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state))
                    ),
                TextColumn::make('overtime_hours')
                    ->label(__('attendances::filament/clusters/reporting/resources/attendance-report.table.columns.overtime-hours'))
                    ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state))
                    ->sortable()
                    ->summarize(
                        Sum::make()
                            ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state))
                    ),
                TextColumn::make('validated_overtime_hours')
                    ->label(__('attendances::filament/clusters/reporting/resources/attendance-report.table.columns.validated-overtime-hours'))
                    ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state))
                    ->sortable()
                    ->toggleable()
                    ->summarize(
                        Sum::make()
                            ->formatStateUsing(fn ($state) => AttendanceResource::formatHours($state))
                    ),
            ])
            ->defaultSort('check_in', 'desc')
            ->defaultGroup('employee.name')
            ->filters([
                Filter::make('check_in')
                    ->schema([
                        DatePicker::make('from')
                            ->label(__('attendances::filament/clusters/reporting/resources/attendance-report.table.filters.from'))
                            ->native(false),
                        DatePicker::make('until')
                            ->label(__('attendances::filament/clusters/reporting/resources/attendance-report.table.filters.until'))
                            ->native(false),
                    ])
                    ->query(fn (Builder $query, array $data): Builder => $query
                        ->when($data['from'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate('check_in', '>=', $date))
                        ->when($data['until'] ?? null, fn (Builder $query, $date): Builder => $query->whereDate('check_in', '<=', $date)))
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['from'] ?? null) {
                            $indicators[] = __('attendances::filament/clusters/reporting/resources/attendance-report.table.filters.from').': '.$data['from'];
                        }

                        if ($data['until'] ?? null) {
                            $indicators[] = __('attendances::filament/clusters/reporting/resources/attendance-report.table.filters.until').': '.$data['until'];
                        }

                        return $indicators;
                    }),
                SelectFilter::make('employee_id')
                    ->label(__('attendances::filament/clusters/reporting/resources/attendance-report.table.filters.employee'))
                    ->relationship('employee', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                SelectFilter::make('department')
                    ->label(__('attendances::filament/clusters/reporting/resources/attendance-report.table.filters.department'))
                    ->options(fn () => Department::query()->pluck('name', 'id')->all())
                    ->searchable()
                    ->multiple()
                    ->query(fn (Builder $query, array $data): Builder => $query->when(
                        $data['values'] ?? null,
                        fn (Builder $query, array $values): Builder => $query->whereHas('employee', fn (Builder $query) => $query->whereIn('department_id', $values)),
                    )),
            ])
            ->groups([
                Group::make('employee.name')
                    ->label(__('attendances::filament/clusters/reporting/resources/attendance-report.table.groups.employee'))
                    ->collapsible(),
                Group::make('employee.department.name')
                    ->label(__('attendances::filament/clusters/reporting/resources/attendance-report.table.groups.department'))
                    ->collapsible(),
                Group::make('employee.attendanceManager.name')
                    ->label(__('attendances::filament/clusters/reporting/resources/attendance-report.table.groups.manager'))
                    ->collapsible(),
                Group::make('check_in')
                    ->label(__('attendances::filament/clusters/reporting/resources/attendance-report.table.groups.day'))
                    ->date()
                    ->collapsible(),
            ])
            ->toolbarActions([
                ExportAction::make()
                    ->label(__('attendances::filament/clusters/reporting/resources/attendance-report.table.toolbar-actions.export.label'))
                    ->icon('heroicon-o-arrow-up-tray')
                    ->exporter(AttendanceExporter::class),
            ]);
    }
}
