<?php

return [
    'title' => 'Manage Attendance',
    'form'  => [
        'sections'                                => [
            'modes'    => [
                'title' => 'Attendance Modes',
            ],
            'overtime' => [
                'title' => 'Overtime',
            ],
        ],
        'enable-top-bar'                          => 'Check In/Out from Top Bar',
        'enable-top-bar-helper-text'              => 'Let employees check in and out from the top bar of the admin panel',
        'auto-check-out'                          => 'Automatic Check-Out',
        'auto-check-out-helper-text'              => 'Close sessions left open longer than the expected working hours plus the tolerance',
        'auto-check-out-tolerance'                => 'Tolerance',
        'hours'                                   => 'Hours',
        'minutes'                                 => 'Minutes',
        'absence-management'                      => 'Absence Management',
        'absence-management-helper-text'          => 'Record a shortfall for working days without any attendance',
        'overtime-company-threshold'              => 'Tolerance in Favor of Company',
        'overtime-company-threshold-helper-text'  => 'Extra time below this value is not counted as overtime',
        'overtime-employee-threshold'             => 'Tolerance in Favor of Employee',
        'overtime-employee-threshold-helper-text' => 'Missing time below this value is not counted as a shortfall',
        'display-overtime'                        => 'Display Overtime',
        'display-overtime-helper-text'            => 'Show overtime to employees',
        'overtime-validation'                     => 'Overtime Validation',
        'overtime-start-date'                     => 'Count Overtime From',
        'overtime-start-date-helper-text'         => 'Days before this date get no overtime',
    ],
];
