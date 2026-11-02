<?php

return [
    'model-label' => 'Horas extras',
    'navigation'  => [
        'title' => 'Registro de horas extras',
    ],
    'form'        => [
        'fields' => [
            'employee'        => 'Funcionário',
            'date'            => 'Data',
            'duration'        => 'Duração (horas)',
            'duration-helper' => 'Use um valor negativo para remover horas.',
        ],
    ],
    'table'       => [
        'columns'      => [
            'employee'      => 'Funcionário',
            'date'          => 'Data',
            'duration'      => 'Horas extras',
            'duration-real' => 'Duração real',
            'adjustment'    => 'Ajuste',
        ],
        'filters'      => [
            'employee'   => 'Funcionário',
            'adjustment' => 'Ajuste',
        ],
        'groups'       => [
            'employee' => 'Funcionário',
            'date'     => 'Data',
        ],
        'actions'      => [
            'edit'   => [
                'notification' => [
                    'title' => 'Ajuste atualizado',
                    'body'  => 'O ajuste de horas extras foi atualizado com sucesso.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Ajuste excluído',
                    'body'  => 'O ajuste de horas extras foi excluído com sucesso.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Ajustes excluídos',
                    'body'  => 'Os ajustes de horas extras foram excluídos com sucesso.',
                ],
            ],
        ],
    ],
];
