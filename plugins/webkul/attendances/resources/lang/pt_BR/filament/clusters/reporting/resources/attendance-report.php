<?php

return [
    'model-label' => 'Relatório de presença',
    'navigation'  => [
        'title' => 'Análise de presença',
    ],
    'table' => [
        'columns' => [
            'employee'                 => 'Funcionário',
            'department'               => 'Departamento',
            'check-in'                 => 'Entrada',
            'check-out'                => 'Saída',
            'worked-hours'             => 'Horas trabalhadas',
            'expected-hours'           => 'Horas previstas',
            'overtime-hours'           => 'Horas extras',
            'validated-overtime-hours' => 'Horas extras validadas',
        ],
        'filters' => [
            'from'       => 'De',
            'until'      => 'Até',
            'employee'   => 'Funcionário',
            'department' => 'Departamento',
        ],
        'groups' => [
            'employee'   => 'Funcionário',
            'department' => 'Departamento',
            'manager'    => 'Gestor de presença',
            'day'        => 'Dia',
        ],
        'toolbar-actions' => [
            'export' => [
                'label' => 'Exportar',
            ],
        ],
    ],
];
