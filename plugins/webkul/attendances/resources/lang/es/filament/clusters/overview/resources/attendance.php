<?php

return [
    'model-label'   => 'Asistencia',
    'navigation'    => [
        'title' => 'Asistencias',
    ],
    'global-search' => [
        'check-in'  => 'Entrada',
        'check-out' => 'Salida',
    ],
    'form'          => [
        'fields' => [
            'employee'    => 'Empleado',
            'check-in'    => 'Entrada',
            'check-out'   => 'Salida',
            'edit-reason' => 'Motivo de la edición',
        ],
    ],
    'infolist'      => [
        'sections' => [
            'overtime' => 'Horas extra',
            'details'  => 'Detalles de entrada y salida',
        ],
        'entries'  => [
            'employee'                 => 'Empleado',
            'check-in'                 => 'Entrada',
            'check-out'                => 'Salida',
            'worked-hours'             => 'Horas trabajadas',
            'expected-hours'           => 'Horas previstas',
            'edit-reason'              => 'Motivo de la edición',
            'overtime-hours'           => 'Horas extra',
            'validated-overtime-hours' => 'Horas extra validadas',
            'overtime-status'          => 'Estado de horas extra',
            'mode'                     => 'Método',
            'ip-address'               => 'Dirección IP',
            'browser'                  => 'Navegador',
        ],
    ],
    'table'         => [
        'columns'         => [
            'employee'        => 'Empleado',
            'check-in'        => 'Entrada',
            'check-out'       => 'Salida',
            'worked-hours'    => 'Horas trabajadas',
            'overtime-hours'  => 'Horas extra',
            'overtime-status' => 'Estado de horas extra',
            'in-mode'         => 'Método de entrada',
            'out-mode'        => 'Método de salida',
            'edit-reason'     => 'Motivo de la edición',
        ],
        'filters'         => [
            'employee'        => 'Empleado',
            'overtime-status' => 'Estado de horas extra',
            'in-mode'         => 'Método de entrada',
            'out-mode'        => 'Método de salida',
        ],
        'groups'          => [
            'employee'   => 'Empleado',
            'department' => 'Departamento',
            'manager'    => 'Responsable de asistencia',
            'method'     => 'Método de entrada',
            'check-in'   => 'Día',
        ],
        'actions'         => [
            'restore' => [
                'notification' => [
                    'title' => 'Asistencia restaurada',
                    'body'  => 'La asistencia se ha restaurado correctamente.',
                ],
            ],
            'delete'  => [
                'notification' => [
                    'title' => 'Asistencia eliminada',
                    'body'  => 'La asistencia se ha eliminado correctamente.',
                ],
            ],
        ],
        'bulk-actions'    => [
            'restore' => [
                'notification' => [
                    'title' => 'Asistencias restauradas',
                    'body'  => 'Las asistencias se han restaurado correctamente.',
                ],
                'partial'      => 'Algunas asistencias no se pudieron restaurar',
            ],
            'delete'  => [
                'notification' => [
                    'title' => 'Asistencias eliminadas',
                    'body'  => 'Las asistencias se han eliminado correctamente.',
                ],
            ],
        ],
        'toolbar-actions' => [
            'export' => [
                'label' => 'Exportar',
            ],
        ],
    ],
];
