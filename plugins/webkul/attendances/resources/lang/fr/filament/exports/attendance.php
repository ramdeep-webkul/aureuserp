<?php

return [
    'columns'      => [
        'employee'                 => 'Employé',
        'check-in'                 => 'Arrivée',
        'check-out'                => 'Départ',
        'worked-hours'             => 'Heures travaillées',
        'expected-hours'           => 'Heures prévues',
        'overtime-hours'           => 'Heures supplémentaires',
        'validated-overtime-hours' => 'Heures supplémentaires validées',
        'overtime-status'          => 'Statut des heures supplémentaires',
        'in-mode'                  => 'Mode d\'arrivée',
        'out-mode'                 => 'Mode de départ',
        'edit-reason'              => 'Motif de modification',
        'company'                  => 'Société',
    ],
    'notification' => [
        'completed' => 'L\'export des présences est terminé et :count lignes ont été exportées.',
        'failed'    => ':count lignes n\'ont pas pu être exportées.',
    ],
];
