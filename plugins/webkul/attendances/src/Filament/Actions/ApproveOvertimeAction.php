<?php

namespace Webkul\Attendance\Filament\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Webkul\Attendance\Enums\OvertimeStatus;
use Webkul\Attendance\Exceptions\AttendanceAccessException;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Services\AttendanceWorkflow;

class ApproveOvertimeAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'approveOvertime';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('attendances::filament/actions/overtime.approve.label'))
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->visible(fn (Attendance $record): bool => ! $record->trashed()
                && abs((float) $record->overtime_hours) > 0
                && $record->overtime_status !== OvertimeStatus::APPROVED)
            ->authorize(fn (Attendance $record): bool => auth()->user()?->can('approve', $record) ?? false)
            ->schema([
                TextInput::make('validated_overtime_hours')
                    ->label(__('attendances::filament/actions/overtime.approve.form.validated-overtime-hours'))
                    ->numeric()
                    ->required()
                    ->default(fn (Attendance $record): float => $record->overtime_status === OvertimeStatus::REFUSED
                        ? (float) $record->overtime_hours
                        : (float) $record->validated_overtime_hours),
            ])
            ->action(function (Attendance $record, array $data): void {
                try {
                    app(AttendanceWorkflow::class)->approveOvertime($record, (float) $data['validated_overtime_hours']);
                } catch (AttendanceAccessException $e) {
                    Notification::make()
                        ->danger()
                        ->title($e->getMessage())
                        ->send();

                    $this->halt();
                }

                Notification::make()
                    ->success()
                    ->title(__('attendances::filament/actions/overtime.approve.notification.title'))
                    ->body(__('attendances::filament/actions/overtime.approve.notification.body'))
                    ->send();
            });
    }
}
