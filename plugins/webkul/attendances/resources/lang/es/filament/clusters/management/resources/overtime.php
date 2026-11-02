<?php

return [
    'model-label' => 'Horas extra',
    'navigation'  => [
        'title' => 'Registro de horas extra',
    ],
    'form'        => [
        'fields' => [
            'employee'        => 'Empleado',
            'date'            => 'Fecha',
            'duration'        => 'Duración (horas)',
            'duration-helper' => 'Use un valor negativo para restar horas.',
        ],
    ],
    'table'       => [
        'columns'      => [
            'employee'      => 'Empleado',
            'date'          => 'Fecha',
            'duration'      => 'Horas extra',
            'duration-real' => 'Duración real',
            'adjustment'    => 'Ajuste',
        ],
        'filters'      => [
            'employee'   => 'Empleado',
            'adjustment' => 'Ajuste',
        ],
        'groups'       => [
            'employee' => 'Empleado',
            'date'     => 'Fecha',
        ],
        'actions'      => [
            'edit'   => [
                'notification' => [
                    'title' => 'Ajuste actualizado',
                    'body'  => 'El ajuste de horas extra se ha actualizado correctamente.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Ajuste eliminado',
                    'body'  => 'El ajuste de horas extra se ha eliminado correctamente.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Ajustes eliminados',
                    'body'  => 'Los ajustes de horas extra se han eliminado correctamente.',
                ],
            ],
        ],
    ],
];
