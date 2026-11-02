<?php

return [
    'model-label' => 'تقرير الحضور',
    'navigation'  => [
        'title' => 'تحليل الحضور',
    ],
    'table' => [
        'columns' => [
            'employee'                 => 'الموظف',
            'department'               => 'القسم',
            'check-in'                 => 'تسجيل الدخول',
            'check-out'                => 'تسجيل الخروج',
            'worked-hours'             => 'ساعات العمل',
            'expected-hours'           => 'الساعات المتوقعة',
            'overtime-hours'           => 'الوقت الإضافي',
            'validated-overtime-hours' => 'الوقت الإضافي المعتمد',
        ],
        'filters' => [
            'from'       => 'من',
            'until'      => 'حتى',
            'employee'   => 'الموظف',
            'department' => 'القسم',
        ],
        'groups' => [
            'employee'   => 'الموظف',
            'department' => 'القسم',
            'manager'    => 'مدير الحضور',
            'day'        => 'اليوم',
        ],
        'toolbar-actions' => [
            'export' => [
                'label' => 'تصدير',
            ],
        ],
    ],
];
