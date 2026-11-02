<?php

return [
    'workflow' => [
        'check-out-before-check-in' => 'لا يمكن أن يكون وقت الانصراف قبل وقت الحضور.',
        'future-check-in'           => 'لا يمكن أن يكون وقت الحضور في المستقبل.',
        'already-checked-in'        => 'لم يسجّل :employee الانصراف منذ :datetime.',
        'overlap'                   => 'لدى :employee حضور يبدأ في :datetime ويتداخل مع هذا الحضور.',
        'not-checked-in'            => ':employee لم يسجّل الحضور.',
        'reason-required'           => 'أدخل سببًا لهذا التصحيح.',
        'assign-denied'             => 'يمكنك تسجيل الحضور لنفسك أو للموظفين الذين تديرهم فقط. :employee ليس منهم.',
        'approve-denied'            => 'يمكن لمدير الحضور الخاص بـ :employee أو لمستخدم لديه صلاحية على جميع السجلات فقط اتخاذ قرار بشأن هذا الوقت الإضافي.',
    ],
];
