<?php

namespace Webkul\Attendance\Filament\Actions;

use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Webkul\Attendance\Enums\OvertimeStatus;
use Webkul\Attendance\Exceptions\AttendanceAccessException;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Services\AttendanceWorkflow;

class RefuseOvertimeAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'refuseOvertime';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('attendances::filament/actions/overtime.refuse.label'))
            ->icon('heroicon-o-x-circle')
            ->color('danger')
            ->requiresConfirmation()
            ->visible(fn (Attendance $record): bool => ! $record->trashed()
                && abs((float) $record->overtime_hours) > 0
                && $record->overtime_status !== OvertimeStatus::REFUSED)
            ->authorize(fn (Attendance $record): bool => auth()->user()?->can('approve', $record) ?? false)
            ->action(function (Attendance $record): void {
                try {
                    app(AttendanceWorkflow::class)->refuseOvertime($record);
                } catch (AttendanceAccessException $e) {
                    Notification::make()
                        ->danger()
                        ->title($e->getMessage())
                        ->send();

                    $this->halt();
                }

                Notification::make()
                    ->success()
                    ->title(__('attendances::filament/actions/overtime.refuse.notification.title'))
                    ->body(__('attendances::filament/actions/overtime.refuse.notification.body'))
                    ->send();
            });
    }
}
