<?php

return [
    'approve' => [
        'label'        => 'Aprovar horas extras',
        'form'         => [
            'validated-overtime-hours' => 'Horas extras validadas (horas)',
        ],
        'notification' => [
            'title' => 'Horas extras aprovadas',
            'body'  => 'As horas extras foram aprovadas.',
        ],
    ],
    'refuse'  => [
        'label'        => 'Recusar horas extras',
        'notification' => [
            'title' => 'Horas extras recusadas',
            'body'  => 'As horas extras foram recusadas.',
        ],
    ],
];
