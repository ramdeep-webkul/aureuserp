<?php

return [
    'model-label' => 'Overtime',
    'navigation'  => [
        'title' => 'Overtime Ledger',
    ],
    'form'        => [
        'fields' => [
            'employee'        => 'Employee',
            'date'            => 'Date',
            'duration'        => 'Duration (hours)',
            'duration-helper' => 'Use a negative value to remove hours.',
        ],
    ],
    'table'       => [
        'columns'      => [
            'employee'      => 'Employee',
            'date'          => 'Date',
            'duration'      => 'Overtime',
            'duration-real' => 'Real Duration',
            'adjustment'    => 'Adjustment',
        ],
        'filters'      => [
            'employee'   => 'Employee',
            'adjustment' => 'Adjustment',
        ],
        'groups'       => [
            'employee' => 'Employee',
            'date'     => 'Date',
        ],
        'actions'      => [
            'edit'   => [
                'notification' => [
                    'title' => 'Adjustment updated',
                    'body'  => 'The overtime adjustment has been updated successfully.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Adjustment deleted',
                    'body'  => 'The overtime adjustment has been deleted successfully.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Adjustments deleted',
                    'body'  => 'The overtime adjustments have been deleted successfully.',
                ],
            ],
        ],
    ],
];
