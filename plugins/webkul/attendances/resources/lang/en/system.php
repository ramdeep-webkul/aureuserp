<?php

return [
    'workflow' => [
        'check-out-before-check-in' => 'Check-out cannot be earlier than check-in.',
        'future-check-in'           => 'Check-in cannot be in the future.',
        'already-checked-in'        => ':employee has not checked out since :datetime.',
        'overlap'                   => ':employee already has an attendance starting :datetime that overlaps this one.',
        'not-checked-in'            => ':employee is not checked in.',
        'reason-required'           => 'Enter a reason for this correction.',
        'assign-denied'             => 'You can only record attendances for yourself or for employees you manage. :employee is not one of them.',
        'approve-denied'            => 'Only the attendance manager of :employee or a user with access to all records can decide on this overtime.',
    ],
];
