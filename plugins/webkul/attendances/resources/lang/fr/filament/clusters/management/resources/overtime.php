<?php

return [
    'model-label' => 'Heures supplémentaires',
    'navigation'  => [
        'title' => 'Registre des heures supplémentaires',
    ],
    'form'        => [
        'fields' => [
            'employee'        => 'Employé',
            'date'            => 'Date',
            'duration'        => 'Durée (heures)',
            'duration-helper' => 'Utilisez une valeur négative pour retirer des heures.',
        ],
    ],
    'table'       => [
        'columns'      => [
            'employee'      => 'Employé',
            'date'          => 'Date',
            'duration'      => 'Heures supplémentaires',
            'duration-real' => 'Durée réelle',
            'adjustment'    => 'Ajustement',
        ],
        'filters'      => [
            'employee'   => 'Employé',
            'adjustment' => 'Ajustement',
        ],
        'groups'       => [
            'employee' => 'Employé',
            'date'     => 'Date',
        ],
        'actions'      => [
            'edit'   => [
                'notification' => [
                    'title' => 'Ajustement mis à jour',
                    'body'  => 'L\'ajustement des heures supplémentaires a été mis à jour avec succès.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'Ajustement supprimé',
                    'body'  => 'L\'ajustement des heures supplémentaires a été supprimé avec succès.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'Ajustements supprimés',
                    'body'  => 'Les ajustements des heures supplémentaires ont été supprimés avec succès.',
                ],
            ],
        ],
    ],
];
