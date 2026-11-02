<?php

return [
    'model-label' => 'Attendance Report',
    'navigation'  => [
        'title' => 'Attendance Analysis',
    ],
    'table' => [
        'columns' => [
            'employee'                 => 'Employee',
            'department'               => 'Department',
            'check-in'                 => 'Check In',
            'check-out'                => 'Check Out',
            'worked-hours'             => 'Worked Hours',
            'expected-hours'           => 'Expected Hours',
            'overtime-hours'           => 'Overtime',
            'validated-overtime-hours' => 'Validated Overtime',
        ],
        'filters' => [
            'from'       => 'From',
            'until'      => 'Until',
            'employee'   => 'Employee',
            'department' => 'Department',
        ],
        'groups' => [
            'employee'   => 'Employee',
            'department' => 'Department',
            'manager'    => 'Attendance Manager',
            'day'        => 'Day',
        ],
        'toolbar-actions' => [
            'export' => [
                'label' => 'Export',
            ],
        ],
    ],
];
