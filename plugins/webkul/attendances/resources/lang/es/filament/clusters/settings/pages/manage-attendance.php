<?php

return [
    'title' => 'Gestionar asistencia',
    'form'  => [
        'sections'                                => [
            'modes'    => [
                'title' => 'Modos de asistencia',
            ],
            'overtime' => [
                'title' => 'Horas extra',
            ],
        ],
        'enable-top-bar'                          => 'Registrar entrada/salida desde la barra superior',
        'enable-top-bar-helper-text'              => 'Permite a los empleados registrar entrada y salida desde la barra superior del panel de administración',
        'auto-check-out'                          => 'Salida automática',
        'auto-check-out-helper-text'              => 'Cierra las sesiones abiertas más tiempo que las horas de trabajo previstas más la tolerancia',
        'auto-check-out-tolerance'                => 'Tolerancia',
        'hours'                                   => 'Horas',
        'minutes'                                 => 'Minutos',
        'absence-management'                      => 'Gestión de ausencias',
        'absence-management-helper-text'          => 'Registra un déficit en los días laborables sin ninguna asistencia',
        'overtime-company-threshold'              => 'Tolerancia a favor de la empresa',
        'overtime-company-threshold-helper-text'  => 'El tiempo extra inferior a este valor no cuenta como horas extra',
        'overtime-employee-threshold'             => 'Tolerancia a favor del empleado',
        'overtime-employee-threshold-helper-text' => 'El tiempo faltante inferior a este valor no cuenta como déficit',
        'display-overtime'                        => 'Mostrar horas extra',
        'display-overtime-helper-text'            => 'Muestra las horas extra a los empleados',
        'overtime-validation'                     => 'Validación de horas extra',
        'overtime-start-date'                     => 'Contar horas extra desde',
        'overtime-start-date-helper-text'         => 'Los días anteriores a esta fecha no generan horas extra',
    ],
];
