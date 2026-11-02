<?php

namespace Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource\Pages;

use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Attendance\Filament\Actions\ApproveOvertimeAction;
use Webkul\Attendance\Filament\Actions\RefuseOvertimeAction;
use Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource;
use Webkul\Attendance\Models\Attendance;
use Webkul\Attendance\Services\AttendanceWorkflow;
use Webkul\Chatter\Filament\Actions\ChatterAction;

class ViewAttendance extends ViewRecord
{
    protected static string $resource = AttendanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ChatterAction::make()
                ->resource(static::$resource)
                ->activityPlans($this->getRecord()->activityPlans()),
            ApproveOvertimeAction::make(),
            RefuseOvertimeAction::make(),
            EditAction::make(),
            DeleteAction::make()
                ->using(fn (Attendance $record) => app(AttendanceWorkflow::class)->delete($record))
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('attendances::filament/clusters/overview/resources/attendance/pages/view-attendance.header-actions.delete.notification.title'))
                        ->body(__('attendances::filament/clusters/overview/resources/attendance/pages/view-attendance.header-actions.delete.notification.body'))
                ),
        ];
    }
}
