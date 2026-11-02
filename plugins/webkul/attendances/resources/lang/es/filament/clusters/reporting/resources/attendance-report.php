<?php

return [
    'model-label' => 'Informe de asistencia',
    'navigation'  => [
        'title' => 'Análisis de asistencia',
    ],
    'table' => [
        'columns' => [
            'employee'                 => 'Empleado',
            'department'               => 'Departamento',
            'check-in'                 => 'Entrada',
            'check-out'                => 'Salida',
            'worked-hours'             => 'Horas trabajadas',
            'expected-hours'           => 'Horas esperadas',
            'overtime-hours'           => 'Horas extra',
            'validated-overtime-hours' => 'Horas extra validadas',
        ],
        'filters' => [
            'from'       => 'Desde',
            'until'      => 'Hasta',
            'employee'   => 'Empleado',
            'department' => 'Departamento',
        ],
        'groups' => [
            'employee'   => 'Empleado',
            'department' => 'Departamento',
            'manager'    => 'Responsable de asistencia',
            'day'        => 'Día',
        ],
        'toolbar-actions' => [
            'export' => [
                'label' => 'Exportar',
            ],
        ],
    ],
];
