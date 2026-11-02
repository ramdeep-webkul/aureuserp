<?php

return [
    'model-label'   => 'Presença',
    'navigation'    => [
        'title' => 'Presenças',
    ],
    'global-search' => [
        'check-in'  => 'Entrada',
        'check-out' => 'Saída',
    ],
    'form'          => [
        'fields' => [
            'employee'    => 'Funcionário',
            'check-in'    => 'Entrada',
            'check-out'   => 'Saída',
            'edit-reason' => 'Motivo da edição',
        ],
    ],
    'infolist'      => [
        'sections' => [
            'overtime' => 'Horas extras',
            'details'  => 'Detalhes de entrada e saída',
        ],
        'entries'  => [
            'employee'                 => 'Funcionário',
            'check-in'                 => 'Entrada',
            'check-out'                => 'Saída',
            'worked-hours'             => 'Horas trabalhadas',
            'expected-hours'           => 'Horas previstas',
            'edit-reason'              => 'Motivo da edição',
            'overtime-hours'           => 'Horas extras',
            'validated-overtime-hours' => 'Horas extras validadas',
            'overtime-status'          => 'Status das horas extras',
            'mode'                     => 'Método',
            'ip-address'               => 'Endereço IP',
            'browser'                  => 'Navegador',
        ],
    ],
    'table'         => [
        'columns'         => [
            'employee'        => 'Funcionário',
            'check-in'        => 'Entrada',
            'check-out'       => 'Saída',
            'worked-hours'    => 'Horas trabalhadas',
            'overtime-hours'  => 'Horas extras',
            'overtime-status' => 'Status das horas extras',
            'in-mode'         => 'Método de entrada',
            'out-mode'        => 'Método de saída',
            'edit-reason'     => 'Motivo da edição',
        ],
        'filters'         => [
            'employee'        => 'Funcionário',
            'overtime-status' => 'Status das horas extras',
            'in-mode'         => 'Método de entrada',
            'out-mode'        => 'Método de saída',
        ],
        'groups'          => [
            'employee'   => 'Funcionário',
            'department' => 'Departamento',
            'manager'    => 'Responsável pela presença',
            'method'     => 'Método de entrada',
            'check-in'   => 'Dia',
        ],
        'actions'         => [
            'restore' => [
                'notification' => [
                    'title' => 'Presença restaurada',
                    'body'  => 'A presença foi restaurada com sucesso.',
                ],
            ],
            'delete'  => [
                'notification' => [
                    'title' => 'Presença excluída',
                    'body'  => 'A presença foi excluída com sucesso.',
                ],
            ],
        ],
        'bulk-actions'    => [
            'restore' => [
                'notification' => [
                    'title' => 'Presenças restauradas',
                    'body'  => 'As presenças foram restauradas com sucesso.',
                ],
                'partial'      => 'Algumas presenças não puderam ser restauradas',
            ],
            'delete'  => [
                'notification' => [
                    'title' => 'Presenças excluídas',
                    'body'  => 'As presenças foram excluídas com sucesso.',
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
