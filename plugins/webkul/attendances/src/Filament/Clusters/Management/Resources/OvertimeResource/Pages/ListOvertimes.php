<?php

namespace Webkul\Attendance\Filament\Clusters\Management\Resources\OvertimeResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Webkul\Attendance\Filament\Clusters\Management\Resources\OvertimeResource;
use Webkul\Attendance\Models\Overtime;
use Webkul\Employee\Models\Employee;

class ListOvertimes extends ListRecords
{
    protected static string $resource = OvertimeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('attendances::filament/clusters/management/resources/overtime/pages/list-overtimes.header-actions.create.label'))
                ->icon('heroicon-o-plus-circle')
                ->using(function (array $data): Overtime {
                    return Overtime::create([
                        ...$data,
                        'company_id'    => Employee::find($data['employee_id'])?->company_id,
                        'duration_real' => $data['duration'],
                        'adjustment'    => true,
                    ]);
                })
                ->successNotification(
                    Notification::make()
                        ->success()
                        ->title(__('attendances::filament/clusters/management/resources/overtime/pages/list-overtimes.header-actions.create.notification.title'))
                        ->body(__('attendances::filament/clusters/management/resources/overtime/pages/list-overtimes.header-actions.create.notification.body'))
                ),
        ];
    }
}
