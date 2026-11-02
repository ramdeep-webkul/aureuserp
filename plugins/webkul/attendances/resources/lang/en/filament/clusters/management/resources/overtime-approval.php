<?php

return [
    'model-label' => 'Overtime Approval',
    'navigation'  => [
        'title' => 'Overtime Approvals',
    ],
    'table'       => [
        'columns'      => [
            'employee'                 => 'Employee',
            'check-in'                 => 'Check In',
            'check-out'                => 'Check Out',
            'worked-hours'             => 'Worked Hours',
            'overtime-hours'           => 'Overtime',
            'validated-overtime-hours' => 'Validated Overtime',
            'overtime-status'          => 'Overtime Status',
        ],
        'filters'      => [
            'employee' => 'Employee',
        ],
        'groups'       => [
            'employee'        => 'Employee',
            'overtime-status' => 'Overtime Status',
        ],
        'bulk-actions' => [
            'approve'      => [
                'label' => 'Approve',
            ],
            'refuse'       => [
                'label' => 'Refuse',
            ],
            'notification' => [
                'title' => 'Overtime updated',
                'body'  => ':count updated, :skipped skipped (no approval right).',
            ],
        ],
    ],
];
