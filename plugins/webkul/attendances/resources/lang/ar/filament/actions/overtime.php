<?php

return [
    'approve' => [
        'label'        => 'اعتماد العمل الإضافي',
        'form'         => [
            'validated-overtime-hours' => 'العمل الإضافي المعتمد (ساعات)',
        ],
        'notification' => [
            'title' => 'تم اعتماد العمل الإضافي',
            'body'  => 'تم اعتماد العمل الإضافي.',
        ],
    ],
    'refuse'  => [
        'label'        => 'رفض العمل الإضافي',
        'notification' => [
            'title' => 'تم رفض العمل الإضافي',
            'body'  => 'تم رفض العمل الإضافي.',
        ],
    ],
];
