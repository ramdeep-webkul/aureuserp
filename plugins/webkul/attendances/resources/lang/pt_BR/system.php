<?php

return [
    'workflow' => [
        'check-out-before-check-in' => 'A saída não pode ser anterior à entrada.',
        'future-check-in'           => 'A entrada não pode estar no futuro.',
        'already-checked-in'        => ':employee não registrou a saída desde :datetime.',
        'overlap'                   => ':employee já tem uma presença iniciando em :datetime que se sobrepõe a esta.',
        'not-checked-in'            => ':employee não registrou a entrada.',
        'reason-required'           => 'Informe um motivo para esta correção.',
        'assign-denied'             => 'Você só pode registrar presenças para si mesmo ou para os funcionários que gerencia. :employee não é um deles.',
        'approve-denied'            => 'Somente o gerente de presença de :employee ou um usuário com acesso a todos os registros pode decidir sobre estas horas extras.',
    ],
];
