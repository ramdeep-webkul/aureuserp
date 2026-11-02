<?php

return [
    'model-label' => 'Aprobación de horas extra',
    'navigation'  => [
        'title' => 'Aprobaciones de horas extra',
    ],
    'table'       => [
        'columns'      => [
            'employee'                 => 'Empleado',
            'check-in'                 => 'Entrada',
            'check-out'                => 'Salida',
            'worked-hours'             => 'Horas trabajadas',
            'overtime-hours'           => 'Horas extra',
            'validated-overtime-hours' => 'Horas extra validadas',
            'overtime-status'          => 'Estado de horas extra',
        ],
        'filters'      => [
            'employee' => 'Empleado',
        ],
        'groups'       => [
            'employee'        => 'Empleado',
            'overtime-status' => 'Estado de horas extra',
        ],
        'bulk-actions' => [
            'approve'      => [
                'label' => 'Aprobar',
            ],
            'refuse'       => [
                'label' => 'Rechazar',
            ],
            'notification' => [
                'title' => 'Horas extra actualizadas',
                'body'  => ':count actualizadas, :skipped omitidas (sin permiso de aprobación).',
            ],
        ],
    ],
];
