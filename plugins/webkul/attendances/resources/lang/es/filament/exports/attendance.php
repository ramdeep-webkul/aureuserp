<?php

return [
    'columns'      => [
        'employee'                 => 'Empleado',
        'check-in'                 => 'Entrada',
        'check-out'                => 'Salida',
        'worked-hours'             => 'Horas trabajadas',
        'expected-hours'           => 'Horas previstas',
        'overtime-hours'           => 'Horas extra',
        'validated-overtime-hours' => 'Horas extra validadas',
        'overtime-status'          => 'Estado de horas extra',
        'in-mode'                  => 'Método de entrada',
        'out-mode'                 => 'Método de salida',
        'edit-reason'              => 'Motivo de la edición',
        'company'                  => 'Empresa',
    ],
    'notification' => [
        'completed' => 'La exportación de asistencias ha finalizado y se exportaron :count filas.',
        'failed'    => ':count filas no se pudieron exportar.',
    ],
];
