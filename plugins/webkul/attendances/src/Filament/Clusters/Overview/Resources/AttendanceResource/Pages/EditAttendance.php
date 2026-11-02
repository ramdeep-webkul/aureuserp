<?php

namespace Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Webkul\Attendance\Exceptions\AttendanceAccessException;
use Webkul\Attendance\Exceptions\AttendanceValidationException;
use Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Services\AttendanceWorkflow;
use Webkul\Chatter\Filament\Actions\ChatterAction;

class EditAttendance extends EditRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('attendances::filament/clusters/overview/resources/attendance/pages/edit-attendance.notification.title'))
            ->body(__('attendances::filament/clusters/overview/resources/attendance/pages/edit-attendance.notification.body'));
    }

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource(static::$resource)
                ->activityPlans($this->getRecord()->activityPlans()),
            ViewAction::make(),
            DeleteAction::make()
                ->using(fn (Attendance $record) => app(AttendanceWorkflow::class)->delete($record))
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('attendances::filament/clusters/overview/resources/attendance/pages/edit-attendance.header-actions.delete.notification.title'))
                        ->body(__('attendances::filament/clusters/overview/resources/attendance/pages/edit-attendance.header-actions.delete.notification.body'))
                ),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $data['edit_reason'] = null;

        return $data;
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        try {
            $attendance = app(AttendanceWorkflow::class)->correct(
                $record,
                Arr::only($data, ['employee_id', 'check_in', 'check_out']),
                $data['edit_reason'] ?? null
            );
        } catch (AttendanceValidationException|AttendanceAccessException $e) {
            Notification::make()
                ->danger()
                ->title($e->getMessage())
                ->send();

            $this->halt();
        }

        $attendance->fill(Arr::except($data, ['employee_id', 'check_in', 'check_out', 'edit_reason']));

        if ($attendance->isDirty()) {
            $attendance->save();
        }

        return $attendance;
    }
}
