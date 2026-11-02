<?php

return [
    'title' => 'Gerenciar presença',
    'form'  => [
        'sections'                                => [
            'modes'    => [
                'title' => 'Modos de presença',
            ],
            'overtime' => [
                'title' => 'Horas extras',
            ],
        ],
        'enable-top-bar'                          => 'Registrar entrada/saída pela barra superior',
        'enable-top-bar-helper-text'              => 'Permite que os funcionários registrem entrada e saída pela barra superior do painel de administração',
        'auto-check-out'                          => 'Saída automática',
        'auto-check-out-helper-text'              => 'Fecha as sessões abertas por mais tempo que as horas de trabalho previstas mais a tolerância',
        'auto-check-out-tolerance'                => 'Tolerância',
        'hours'                                   => 'Horas',
        'minutes'                                 => 'Minutos',
        'absence-management'                      => 'Gestão de ausências',
        'absence-management-helper-text'          => 'Registra um déficit nos dias úteis sem nenhuma presença',
        'overtime-company-threshold'              => 'Tolerância a favor da empresa',
        'overtime-company-threshold-helper-text'  => 'O tempo extra abaixo deste valor não conta como hora extra',
        'overtime-employee-threshold'             => 'Tolerância a favor do funcionário',
        'overtime-employee-threshold-helper-text' => 'O tempo faltante abaixo deste valor não conta como déficit',
        'display-overtime'                        => 'Exibir horas extras',
        'display-overtime-helper-text'            => 'Mostra as horas extras aos funcionários',
        'overtime-validation'                     => 'Validação de horas extras',
        'overtime-start-date'                     => 'Contar horas extras a partir de',
        'overtime-start-date-helper-text'         => 'Os dias anteriores a esta data não geram horas extras',
    ],
];
