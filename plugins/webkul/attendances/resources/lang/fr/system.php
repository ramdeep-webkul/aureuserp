<?php

return [
    'workflow' => [
        'check-out-before-check-in' => 'Le départ ne peut pas être antérieur à l\'arrivée.',
        'future-check-in'           => 'L\'arrivée ne peut pas être dans le futur.',
        'already-checked-in'        => ':employee n\'a pas pointé son départ depuis le :datetime.',
        'overlap'                   => ':employee a déjà une présence commençant le :datetime qui chevauche celle-ci.',
        'not-checked-in'            => ':employee n\'a pas pointé son arrivée.',
        'reason-required'           => 'Saisissez un motif pour cette correction.',
        'assign-denied'             => 'Vous ne pouvez enregistrer des présences que pour vous-même ou pour les employés que vous gérez. :employee n\'en fait pas partie.',
        'approve-denied'            => 'Seul le responsable des présences de :employee ou un utilisateur ayant accès à tous les enregistrements peut décider de ces heures supplémentaires.',
    ],
];
