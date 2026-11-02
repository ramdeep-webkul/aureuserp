<?php

return [
    'title' => 'إدارة الحضور',
    'form'  => [
        'sections'                                => [
            'modes'    => [
                'title' => 'طرق تسجيل الحضور',
            ],
            'overtime' => [
                'title' => 'العمل الإضافي',
            ],
        ],
        'enable-top-bar'                          => 'تسجيل الدخول والخروج من الشريط العلوي',
        'enable-top-bar-helper-text'              => 'السماح للموظفين بتسجيل الدخول والخروج من الشريط العلوي في لوحة الإدارة',
        'auto-check-out'                          => 'تسجيل خروج تلقائي',
        'auto-check-out-helper-text'              => 'إغلاق الجلسات المفتوحة لمدة أطول من ساعات العمل المتوقعة مضافًا إليها هامش السماح',
        'auto-check-out-tolerance'                => 'هامش السماح',
        'hours'                                   => 'ساعات',
        'minutes'                                 => 'دقائق',
        'absence-management'                      => 'إدارة الغياب',
        'absence-management-helper-text'          => 'تسجيل النقص لأيام العمل التي لا يوجد فيها أي حضور',
        'overtime-company-threshold'              => 'هامش السماح لصالح الشركة',
        'overtime-company-threshold-helper-text'  => 'الوقت الإضافي الأقل من هذه القيمة لا يُحتسب عملًا إضافيًا',
        'overtime-employee-threshold'             => 'هامش السماح لصالح الموظف',
        'overtime-employee-threshold-helper-text' => 'الوقت الناقص الأقل من هذه القيمة لا يُحتسب نقصًا',
        'display-overtime'                        => 'عرض العمل الإضافي',
        'display-overtime-helper-text'            => 'إظهار العمل الإضافي للموظفين',
        'overtime-validation'                     => 'اعتماد العمل الإضافي',
        'overtime-start-date'                     => 'احتساب العمل الإضافي اعتبارًا من',
        'overtime-start-date-helper-text'         => 'الأيام السابقة لهذا التاريخ لا يُحتسب لها عمل إضافي',
    ],
];
