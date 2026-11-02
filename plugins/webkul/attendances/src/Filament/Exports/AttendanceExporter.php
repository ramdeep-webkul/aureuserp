<?php

namespace Webkul\Attendance\Filament\Exports;

use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;
use Webkul\Attendance\Models\Attendance;

class AttendanceExporter extends Exporter
{
    protected static ?string $model = Attendance::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('employee.name')
                ->label(__('attendances::filament/exports/attendance.columns.employee')),
            ExportColumn::make('check_in')
                ->label(__('attendances::filament/exports/attendance.columns.check-in')),
            ExportColumn::make('check_out')
                ->label(__('attendances::filament/exports/attendance.columns.check-out')),
            ExportColumn::make('worked_hours')
                ->label(__('attendances::filament/exports/attendance.columns.worked-hours')),
            ExportColumn::make('expected_hours')
                ->label(__('attendances::filament/exports/attendance.columns.expected-hours')),
            ExportColumn::make('overtime_hours')
                ->label(__('attendances::filament/exports/attendance.columns.overtime-hours')),
            ExportColumn::make('validated_overtime_hours')
                ->label(__('attendances::filament/exports/attendance.columns.validated-overtime-hours')),
            ExportColumn::make('overtime_status')
                ->label(__('attendances::filament/exports/attendance.columns.overtime-status'))
                ->formatStateUsing(fn ($state) => is_object($state) ? ($state->getLabel() ?? $state->value ?? '') : (string) $state),
            ExportColumn::make('in_mode')
                ->label(__('attendances::filament/exports/attendance.columns.in-mode'))
                ->formatStateUsing(fn ($state) => is_object($state) ? ($state->getLabel() ?? $state->value ?? '') : (string) $state)
                ->enabledByDefault(false),
            ExportColumn::make('out_mode')
                ->label(__('attendances::filament/exports/attendance.columns.out-mode'))
                ->formatStateUsing(fn ($state) => is_object($state) ? ($state->getLabel() ?? $state->value ?? '') : (string) $state)
                ->enabledByDefault(false),
            ExportColumn::make('edit_reason')
                ->label(__('attendances::filament/exports/attendance.columns.edit-reason'))
                ->enabledByDefault(false),
            ExportColumn::make('company.name')
                ->label(__('attendances::filament/exports/attendance.columns.company'))
                ->enabledByDefault(false),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('attendances::filament/exports/attendance.notification.completed', [
            'count' => number_format($export->successful_rows),
        ]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' '.__('attendances::filament/exports/attendance.notification.failed', [
                'count' => number_format($failedRowsCount),
            ]);
        }

        return $body;
    }
}
