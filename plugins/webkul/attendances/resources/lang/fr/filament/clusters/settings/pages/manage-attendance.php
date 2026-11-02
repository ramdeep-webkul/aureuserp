<?php

return [
    'title' => 'Gérer les présences',
    'form'  => [
        'sections'                                => [
            'modes'    => [
                'title' => 'Modes de présence',
            ],
            'overtime' => [
                'title' => 'Heures supplémentaires',
            ],
        ],
        'enable-top-bar'                          => 'Arrivée/départ depuis la barre supérieure',
        'enable-top-bar-helper-text'              => 'Permet aux employés d\'enregistrer leur arrivée et leur départ depuis la barre supérieure du panneau d\'administration',
        'auto-check-out'                          => 'Départ automatique',
        'auto-check-out-helper-text'              => 'Ferme les sessions ouvertes plus longtemps que les heures de travail prévues plus la tolérance',
        'auto-check-out-tolerance'                => 'Tolérance',
        'hours'                                   => 'Heures',
        'minutes'                                 => 'Minutes',
        'absence-management'                      => 'Gestion des absences',
        'absence-management-helper-text'          => 'Enregistre un déficit pour les jours ouvrés sans aucune présence',
        'overtime-company-threshold'              => 'Tolérance en faveur de l\'entreprise',
        'overtime-company-threshold-helper-text'  => 'Le temps supplémentaire inférieur à cette valeur n\'est pas compté comme heures supplémentaires',
        'overtime-employee-threshold'             => 'Tolérance en faveur de l\'employé',
        'overtime-employee-threshold-helper-text' => 'Le temps manquant inférieur à cette valeur n\'est pas compté comme déficit',
        'display-overtime'                        => 'Afficher les heures supplémentaires',
        'display-overtime-helper-text'            => 'Affiche les heures supplémentaires aux employés',
        'overtime-validation'                     => 'Validation des heures supplémentaires',
        'overtime-start-date'                     => 'Compter les heures supplémentaires à partir du',
        'overtime-start-date-helper-text'         => 'Les jours antérieurs à cette date ne génèrent pas d\'heures supplémentaires',
    ],
];
