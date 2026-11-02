<?php

return [
    'workflow' => [
        'check-out-before-check-in' => 'La salida no puede ser anterior a la entrada.',
        'future-check-in'           => 'La entrada no puede estar en el futuro.',
        'already-checked-in'        => ':employee no ha registrado la salida desde :datetime.',
        'overlap'                   => ':employee ya tiene una asistencia que empieza el :datetime y se superpone con esta.',
        'not-checked-in'            => ':employee no ha registrado la entrada.',
        'reason-required'           => 'Introduce un motivo para esta corrección.',
        'assign-denied'             => 'Solo puedes registrar asistencias propias o de los empleados que gestionas. :employee no es uno de ellos.',
        'approve-denied'            => 'Solo el responsable de asistencia de :employee o un usuario con acceso a todos los registros puede decidir sobre estas horas extra.',
    ],
];
