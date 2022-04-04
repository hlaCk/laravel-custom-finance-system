<?php

return [
    'plural'   => 'حسابات الشجرة',
    'singular' => 'حساب',
    'fields'   => [
        'name'           => 'الاسم',
        'type'           => "النوع",
        'status'         => 'الحالة',
        'account'        => 'حساب',
        'parent_account' => 'الحساب الرئيسي',
        'sub_accounts'   => 'الحسابات الفرعية',
        'created_at'     => 'تاريخ الانشاء',
        'updated_at'     => 'تاريخ التعديل',
        'deleted_at'     => 'تاريخ الحذف',
    ],
    'statuses' => [
        1 => 'مفعل',
        2 => 'معطل',
    ],
    'types'    => [
        'budget' => 'ميزانية',
        'pnl'    => 'ارباح وخسائر',
    ],

];
