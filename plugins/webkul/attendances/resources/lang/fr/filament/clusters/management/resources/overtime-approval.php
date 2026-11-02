<?php

return [
    'model-label' => 'Validation des heures supplémentaires',
    'navigation'  => [
        'title' => 'Validations des heures supplémentaires',
    ],
    'table'       => [
        'columns'      => [
            'employee'                 => 'Employé',
            'check-in'                 => 'Arrivée',
            'check-out'                => 'Départ',
            'worked-hours'             => 'Heures travaillées',
            'overtime-hours'           => 'Heures supplémentaires',
            'validated-overtime-hours' => 'Heures supplémentaires validées',
            'overtime-status'          => 'Statut des heures supplémentaires',
        ],
        'filters'      => [
            'employee' => 'Employé',
        ],
        'groups'       => [
            'employee'        => 'Employé',
            'overtime-status' => 'Statut des heures supplémentaires',
        ],
        'bulk-actions' => [
            'approve'      => [
                'label' => 'Approuver',
            ],
            'refuse'       => [
                'label' => 'Refuser',
            ],
            'notification' => [
                'title' => 'Heures supplémentaires mises à jour',
                'body'  => ':count mises à jour, :skipped ignorées (pas de droit de validation).',
            ],
        ],
    ],
];
