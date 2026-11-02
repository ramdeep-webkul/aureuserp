<?php

return [
    'approve' => [
        'label'        => 'Valider les heures supplémentaires',
        'form'         => [
            'validated-overtime-hours' => 'Heures supplémentaires validées (heures)',
        ],
        'notification' => [
            'title' => 'Heures supplémentaires validées',
            'body'  => 'Les heures supplémentaires ont été validées.',
        ],
    ],
    'refuse'  => [
        'label'        => 'Refuser les heures supplémentaires',
        'notification' => [
            'title' => 'Heures supplémentaires refusées',
            'body'  => 'Les heures supplémentaires ont été refusées.',
        ],
    ],
];
