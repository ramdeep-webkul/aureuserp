<?php

namespace Webkul\Attendance\Filament\Clusters\Reporting\Resources\AttendanceReportResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Webkul\Attendance\Filament\Clusters\Reporting\Resources\AttendanceReportResource;
use Webkul\Attendance\Filament\Widgets\OvertimeChart;
use Webkul\Attendance\Filament\Widgets\WorkedHoursChart;

class ListAttendanceReports extends ListRecords
{
    protected static string $resource = AttendanceReportResource::class;

    public function getHeaderWidgets(): array
    {
        return [
            WorkedHoursChart::make(),
            OvertimeChart::make(),
        ];
    }
}
