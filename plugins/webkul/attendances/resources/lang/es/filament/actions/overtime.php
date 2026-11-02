<?php

return [
    'approve' => [
        'label'        => 'Aprobar horas extra',
        'form'         => [
            'validated-overtime-hours' => 'Horas extra validadas (horas)',
        ],
        'notification' => [
            'title' => 'Horas extra aprobadas',
            'body'  => 'Las horas extra se han aprobado.',
        ],
    ],
    'refuse'  => [
        'label'        => 'Rechazar horas extra',
        'notification' => [
            'title' => 'Horas extra rechazadas',
            'body'  => 'Las horas extra se han rechazado.',
        ],
    ],
];
