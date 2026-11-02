<?php

return [
    'columns'      => [
        'employee'                 => 'Funcionário',
        'check-in'                 => 'Entrada',
        'check-out'                => 'Saída',
        'worked-hours'             => 'Horas trabalhadas',
        'expected-hours'           => 'Horas previstas',
        'overtime-hours'           => 'Horas extras',
        'validated-overtime-hours' => 'Horas extras validadas',
        'overtime-status'          => 'Status das horas extras',
        'in-mode'                  => 'Método de entrada',
        'out-mode'                 => 'Método de saída',
        'edit-reason'              => 'Motivo da edição',
        'company'                  => 'Empresa',
    ],
    'notification' => [
        'completed' => 'A exportação de presenças foi concluída e :count linhas foram exportadas.',
        'failed'    => ':count linhas falharam na exportação.',
    ],
];
