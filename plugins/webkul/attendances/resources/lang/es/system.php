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
        'auto-checked-out'          => 'Se registró la salida de esta asistencia automáticamente porque el empleado siguió registrado después de su horario laboral y de la tolerancia permitida.',
        'absence-detected'          => 'Esta asistencia se creó automáticamente porque el empleado no registró su entrada en un día laborable programado.',
    ],

    'commands' => [
        'auto-check-out'  => 'Se registró automáticamente la salida de :count asistencia(s).',
        'detect-absences' => 'Se registraron :count ausencia(s).',
    ],
];
