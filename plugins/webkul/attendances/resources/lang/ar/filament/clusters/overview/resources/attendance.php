<?php

return [
    'model-label'   => 'الحضور',
    'navigation'    => [
        'title' => 'سجلات الحضور',
    ],
    'global-search' => [
        'check-in'  => 'تسجيل الدخول',
        'check-out' => 'تسجيل الخروج',
    ],
    'form'          => [
        'fields' => [
            'employee'    => 'الموظف',
            'check-in'    => 'تسجيل الدخول',
            'check-out'   => 'تسجيل الخروج',
            'edit-reason' => 'سبب التعديل',
        ],
    ],
    'infolist'      => [
        'sections' => [
            'overtime' => 'العمل الإضافي',
            'details'  => 'تفاصيل الدخول والخروج',
        ],
        'entries'  => [
            'employee'                 => 'الموظف',
            'check-in'                 => 'تسجيل الدخول',
            'check-out'                => 'تسجيل الخروج',
            'worked-hours'             => 'ساعات العمل',
            'expected-hours'           => 'الساعات المتوقعة',
            'edit-reason'              => 'سبب التعديل',
            'overtime-hours'           => 'العمل الإضافي',
            'validated-overtime-hours' => 'العمل الإضافي المعتمد',
            'overtime-status'          => 'حالة العمل الإضافي',
            'mode'                     => 'الطريقة',
            'ip-address'               => 'عنوان IP',
            'browser'                  => 'المتصفح',
        ],
    ],
    'table'         => [
        'columns'         => [
            'employee'        => 'الموظف',
            'check-in'        => 'تسجيل الدخول',
            'check-out'       => 'تسجيل الخروج',
            'worked-hours'    => 'ساعات العمل',
            'overtime-hours'  => 'العمل الإضافي',
            'overtime-status' => 'حالة العمل الإضافي',
            'in-mode'         => 'طريقة تسجيل الدخول',
            'out-mode'        => 'طريقة تسجيل الخروج',
            'edit-reason'     => 'سبب التعديل',
        ],
        'filters'         => [
            'employee'        => 'الموظف',
            'overtime-status' => 'حالة العمل الإضافي',
            'in-mode'         => 'طريقة تسجيل الدخول',
            'out-mode'        => 'طريقة تسجيل الخروج',
        ],
        'groups'          => [
            'employee'   => 'الموظف',
            'department' => 'القسم',
            'manager'    => 'مسؤول الحضور',
            'method'     => 'طريقة تسجيل الدخول',
            'check-in'   => 'اليوم',
        ],
        'actions'         => [
            'restore' => [
                'notification' => [
                    'title' => 'تمت استعادة الحضور',
                    'body'  => 'تمت استعادة سجل الحضور بنجاح.',
                ],
            ],
            'delete'  => [
                'notification' => [
                    'title' => 'تم حذف الحضور',
                    'body'  => 'تم حذف سجل الحضور بنجاح.',
                ],
            ],
        ],
        'bulk-actions'    => [
            'restore' => [
                'notification' => [
                    'title' => 'تمت استعادة سجلات الحضور',
                    'body'  => 'تمت استعادة سجلات الحضور بنجاح.',
                ],
                'partial'      => 'تعذرت استعادة بعض سجلات الحضور',
            ],
            'delete'  => [
                'notification' => [
                    'title' => 'تم حذف سجلات الحضور',
                    'body'  => 'تم حذف سجلات الحضور بنجاح.',
                ],
            ],
        ],
        'toolbar-actions' => [
            'export' => [
                'label' => 'تصدير',
            ],
        ],
    ],
];
