<?php

return [
    'model-label' => 'Rapport de présence',
    'navigation'  => [
        'title' => 'Analyse des présences',
    ],
    'table' => [
        'columns' => [
            'employee'                 => 'Employé',
            'department'               => 'Département',
            'check-in'                 => 'Arrivée',
            'check-out'                => 'Départ',
            'worked-hours'             => 'Heures travaillées',
            'expected-hours'           => 'Heures prévues',
            'overtime-hours'           => 'Heures supplémentaires',
            'validated-overtime-hours' => 'Heures supplémentaires validées',
        ],
        'filters' => [
            'from'       => 'Du',
            'until'      => 'Au',
            'employee'   => 'Employé',
            'department' => 'Département',
        ],
        'groups' => [
            'employee'   => 'Employé',
            'department' => 'Département',
            'manager'    => 'Responsable des présences',
            'day'        => 'Jour',
        ],
        'toolbar-actions' => [
            'export' => [
                'label' => 'Exporter',
            ],
        ],
    ],
];
