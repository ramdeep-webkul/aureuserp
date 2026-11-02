<?php

return [
    'model-label' => 'العمل الإضافي',
    'navigation'  => [
        'title' => 'سجل العمل الإضافي',
    ],
    'form'        => [
        'fields' => [
            'employee'        => 'الموظف',
            'date'            => 'التاريخ',
            'duration'        => 'المدة (ساعات)',
            'duration-helper' => 'استخدم قيمة سالبة لخصم الساعات.',
        ],
    ],
    'table'       => [
        'columns'      => [
            'employee'      => 'الموظف',
            'date'          => 'التاريخ',
            'duration'      => 'العمل الإضافي',
            'duration-real' => 'المدة الفعلية',
            'adjustment'    => 'تعديل يدوي',
        ],
        'filters'      => [
            'employee'   => 'الموظف',
            'adjustment' => 'تعديل يدوي',
        ],
        'groups'       => [
            'employee' => 'الموظف',
            'date'     => 'التاريخ',
        ],
        'actions'      => [
            'edit'   => [
                'notification' => [
                    'title' => 'تم تحديث التعديل',
                    'body'  => 'تم تحديث تعديل العمل الإضافي بنجاح.',
                ],
            ],
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف التعديل',
                    'body'  => 'تم حذف تعديل العمل الإضافي بنجاح.',
                ],
            ],
        ],
        'bulk-actions' => [
            'delete' => [
                'notification' => [
                    'title' => 'تم حذف التعديلات',
                    'body'  => 'تم حذف تعديلات العمل الإضافي بنجاح.',
                ],
            ],
        ],
    ],
];
