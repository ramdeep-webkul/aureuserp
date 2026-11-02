<?php

return [
    'model-label' => 'اعتماد العمل الإضافي',
    'navigation'  => [
        'title' => 'اعتمادات العمل الإضافي',
    ],
    'table'       => [
        'columns'      => [
            'employee'                 => 'الموظف',
            'check-in'                 => 'تسجيل الدخول',
            'check-out'                => 'تسجيل الخروج',
            'worked-hours'             => 'ساعات العمل',
            'overtime-hours'           => 'العمل الإضافي',
            'validated-overtime-hours' => 'العمل الإضافي المعتمد',
            'overtime-status'          => 'حالة العمل الإضافي',
        ],
        'filters'      => [
            'employee' => 'الموظف',
        ],
        'groups'       => [
            'employee'        => 'الموظف',
            'overtime-status' => 'حالة العمل الإضافي',
        ],
        'bulk-actions' => [
            'approve'      => [
                'label' => 'اعتماد',
            ],
            'refuse'       => [
                'label' => 'رفض',
            ],
            'notification' => [
                'title' => 'تم تحديث العمل الإضافي',
                'body'  => 'تم تحديث :count، وتم تخطي :skipped (لا توجد صلاحية اعتماد).',
            ],
        ],
    ],
];
