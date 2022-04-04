<?php

return [
    'plural' => 'العملاء',
    'singular' => 'عميل',
    'fields' => [
        'name' => 'الاسم',
        'type' => "النوع",
        'status' => 'الحالة',
        'created_at' => 'تاريخ الانشاء',
        'updated_at' => 'تاريخ التعديل',
        'deleted_at' => 'تاريخ الحذف',
    ],
    'statuses' => [
        1 => 'مفعل',
        2 => 'معطل',
    ],
    'types' => [
        'cash' => 'نقدي',
        'credit' => 'اجل',
    ],

];
