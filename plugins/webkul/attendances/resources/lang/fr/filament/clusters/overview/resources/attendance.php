<?php

return [
    'model-label'   => 'Présence',
    'navigation'    => [
        'title' => 'Présences',
    ],
    'global-search' => [
        'check-in'  => 'Arrivée',
        'check-out' => 'Départ',
    ],
    'form'          => [
        'fields' => [
            'employee'    => 'Employé',
            'check-in'    => 'Arrivée',
            'check-out'   => 'Départ',
            'edit-reason' => 'Motif de modification',
        ],
    ],
    'infolist'      => [
        'sections' => [
            'overtime' => 'Heures supplémentaires',
            'details'  => 'Détails d\'arrivée et de départ',
        ],
        'entries'  => [
            'employee'                 => 'Employé',
            'check-in'                 => 'Arrivée',
            'check-out'                => 'Départ',
            'worked-hours'             => 'Heures travaillées',
            'expected-hours'           => 'Heures prévues',
            'edit-reason'              => 'Motif de modification',
            'overtime-hours'           => 'Heures supplémentaires',
            'validated-overtime-hours' => 'Heures supplémentaires validées',
            'overtime-status'          => 'Statut des heures supplémentaires',
            'mode'                     => 'Mode',
            'ip-address'               => 'Adresse IP',
            'browser'                  => 'Navigateur',
        ],
    ],
    'table'         => [
        'columns'         => [
            'employee'        => 'Employé',
            'check-in'        => 'Arrivée',
            'check-out'       => 'Départ',
            'worked-hours'    => 'Heures travaillées',
            'overtime-hours'  => 'Heures supplémentaires',
            'overtime-status' => 'Statut des heures supplémentaires',
            'in-mode'         => 'Mode d\'arrivée',
            'out-mode'        => 'Mode de départ',
            'edit-reason'     => 'Motif de modification',
        ],
        'filters'         => [
            'employee'        => 'Employé',
            'overtime-status' => 'Statut des heures supplémentaires',
            'in-mode'         => 'Mode d\'arrivée',
            'out-mode'        => 'Mode de départ',
        ],
        'groups'          => [
            'employee'   => 'Employé',
            'department' => 'Département',
            'manager'    => 'Responsable des présences',
            'method'     => 'Mode d\'arrivée',
            'check-in'   => 'Jour',
        ],
        'actions'         => [
            'restore' => [
                'notification' => [
                    'title' => 'Présence restaurée',
                    'body'  => 'La présence a été restaurée avec succès.',
                ],
            ],
            'delete'  => [
                'notification' => [
                    'title' => 'Présence supprimée',
                    'body'  => 'La présence a été supprimée avec succès.',
                ],
            ],
        ],
        'bulk-actions'    => [
            'restore' => [
                'notification' => [
                    'title' => 'Présences restaurées',
                    'body'  => 'Les présences ont été restaurées avec succès.',
                ],
                'partial'      => 'Certaines présences n\'ont pas pu être restaurées',
            ],
            'delete'  => [
                'notification' => [
                    'title' => 'Présences supprimées',
                    'body'  => 'Les présences ont été supprimées avec succès.',
                ],
            ],
        ],
        'toolbar-actions' => [
            'export' => [
                'label' => 'Exporter',
            ],
        ],
    ],
];
