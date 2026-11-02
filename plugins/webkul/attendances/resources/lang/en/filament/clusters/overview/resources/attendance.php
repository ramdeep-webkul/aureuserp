<?php

return [
    'model-label'   => 'Attendance',
    'navigation'    => [
        'title' => 'Attendances',
    ],
    'global-search' => [
        'check-in'  => 'Check In',
        'check-out' => 'Check Out',
    ],
    'form'          => [
        'fields' => [
            'employee'    => 'Employee',
            'check-in'    => 'Check In',
            'check-out'   => 'Check Out',
            'edit-reason' => 'Edit Reason',
        ],
    ],
    'infolist'      => [
        'sections' => [
            'overtime' => 'Overtime',
            'details'  => 'Check In and Check Out Details',
        ],
        'entries'  => [
            'employee'                 => 'Employee',
            'check-in'                 => 'Check In',
            'check-out'                => 'Check Out',
            'worked-hours'             => 'Worked Hours',
            'expected-hours'           => 'Expected Hours',
            'edit-reason'              => 'Edit Reason',
            'overtime-hours'           => 'Overtime',
            'validated-overtime-hours' => 'Validated Overtime',
            'overtime-status'          => 'Overtime Status',
            'mode'                     => 'Method',
            'ip-address'               => 'IP Address',
            'browser'                  => 'Browser',
        ],
    ],
    'table'         => [
        'columns'         => [
            'employee'        => 'Employee',
            'check-in'        => 'Check In',
            'check-out'       => 'Check Out',
            'worked-hours'    => 'Worked Hours',
            'overtime-hours'  => 'Overtime',
            'overtime-status' => 'Overtime Status',
            'in-mode'         => 'Check In Method',
            'out-mode'        => 'Check Out Method',
            'edit-reason'     => 'Edit Reason',
        ],
        'filters'         => [
            'employee'        => 'Employee',
            'overtime-status' => 'Overtime Status',
            'in-mode'         => 'Check In Method',
            'out-mode'        => 'Check Out Method',
        ],
        'groups'          => [
            'employee'   => 'Employee',
            'department' => 'Department',
            'manager'    => 'Attendance Manager',
            'method'     => 'Check In Method',
            'check-in'   => 'Day',
        ],
        'actions'         => [
            'restore' => [
                'notification' => [
                    'title' => 'Attendance restored',
                    'body'  => 'The attendance has been restored successfully.',
                ],
            ],
            'delete'  => [
                'notification' => [
                    'title' => 'Attendance deleted',
                    'body'  => 'The attendance has been deleted successfully.',
                ],
            ],
        ],
        'bulk-actions'    => [
            'restore' => [
                'notification' => [
                    'title' => 'Attendances restored',
                    'body'  => 'The attendances have been restored successfully.',
                ],
                'partial'      => 'Some attendances could not be restored',
            ],
            'delete'  => [
                'notification' => [
                    'title' => 'Attendances deleted',
                    'body'  => 'The attendances have been deleted successfully.',
                ],
            ],
        ],
        'toolbar-actions' => [
            'export' => [
                'label' => 'Export',
            ],
        ],
    ],
];
