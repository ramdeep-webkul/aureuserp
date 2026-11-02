<?php

use Webkul\Attendance\Filament\Clusters\Management;
use Webkul\Attendance\Filament\Clusters\Management\Resources\OvertimeApprovalResource;
use Webkul\Attendance\Filament\Clusters\Management\Resources\OvertimeResource;
use Webkul\Attendance\Filament\Clusters\Overview;
use Webkul\Attendance\Filament\Clusters\Overview\Resources\AttendanceResource;
use Webkul\Attendance\Filament\Clusters\Reporting;
use Webkul\Attendance\Filament\Clusters\Reporting\Resources\AttendanceReportResource;

$basic = ['view_any', 'view', 'create', 'update'];
$delete = ['delete', 'delete_any'];
$restore = ['restore', 'restore_any'];

return [
    'resources' => [
        'manage' => [
            AttendanceResource::class => [...$basic, ...$delete, ...$restore],
            OvertimeResource::class   => [...$basic, ...$delete],
        ],
        'exclude' => [
            OvertimeApprovalResource::class,
            AttendanceReportResource::class,
        ],
    ],

    'pages' => [
        'exclude' => [
            Overview::class,
            Management::class,
            Reporting::class,
        ],
    ],
];
