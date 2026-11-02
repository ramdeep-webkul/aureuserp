<?php

namespace Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Webkul\Attendance\Exceptions\AttendanceAccessException;
use Webkul\Attendance\Exceptions\AttendanceValidationException;
use Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource;
use Webkul\Attendance\Services\AttendanceWorkflow;

class CreateAttendance extends CreateRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }

    protected function getCreatedNotification(): Notification
    {
        return Notification::make()
            ->success()
            ->title(__('attendances::filament/clusters/overview/resources/attendance/pages/create-attendance.notification.title'))
            ->body(__('attendances::filament/clusters/overview/resources/attendance/pages/create-attendance.notification.body'));
    }

    protected function handleRecordCreation(array $data): Model
    {
        try {
            $attendance = app(AttendanceWorkflow::class)->create($data);
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
