<?php

return [
    'model-label' => 'Aprovação de horas extras',
    'navigation'  => [
        'title' => 'Aprovações de horas extras',
    ],
    'table'       => [
        'columns'      => [
            'employee'                 => 'Funcionário',
            'check-in'                 => 'Entrada',
            'check-out'                => 'Saída',
            'worked-hours'             => 'Horas trabalhadas',
            'overtime-hours'           => 'Horas extras',
            'validated-overtime-hours' => 'Horas extras validadas',
            'overtime-status'          => 'Status das horas extras',
        ],
        'filters'      => [
            'employee' => 'Funcionário',
        ],
        'groups'       => [
            'employee'        => 'Funcionário',
            'overtime-status' => 'Status das horas extras',
        ],
        'bulk-actions' => [
            'approve'      => [
                'label' => 'Aprovar',
            ],
            'refuse'       => [
                'label' => 'Recusar',
            ],
            'notification' => [
                'title' => 'Horas extras atualizadas',
                'body'  => ':count atualizadas, :skipped ignoradas (sem permissão de aprovação).',
            ],
        ],
    ],
];
