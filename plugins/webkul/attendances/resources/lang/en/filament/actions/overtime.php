<?php

return [
    'approve' => [
        'label'        => 'Approve Overtime',
        'form'         => [
            'validated-overtime-hours' => 'Validated Overtime (hours)',
        ],
        'notification' => [
            'title' => 'Overtime approved',
            'body'  => 'The overtime has been approved.',
        ],
    ],
    'refuse'  => [
        'label'        => 'Refuse Overtime',
        'notification' => [
            'title' => 'Overtime refused',
            'body'  => 'The overtime has been refused.',
        ],
    ],
];
