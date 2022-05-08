<?php

return [
    'plural'   => 'المشاريع',
    'singular' => 'مشروع',
    'fields'   => [
        'name'           => 'الاسم',
        'base_cost'      => 'التكلفة الاساسية',
        'cost'           => 'التكلفة',
        'costs'          => 'التكلفة',
        'project_status' => 'حالة المشروع',
        'status'         => 'الحالة',
        'project_costs'  => 'الاعمال اللاضافية',
        'client'         => 'العميل',
        'contractors'    => 'المقاولين',
        'credits'        => 'الاتمان',
        'expenses'       => 'المصروفات',
        'created_at'     => 'تاريخ الانشاء',
        'updated_at'     => 'تاريخ التعديل',
        'deleted_at'     => 'تاريخ الحذف',

        // region: contractor_project
        'date'       => 'التاريخ',
        'remarks'    => 'البيان',
        'unit'       => 'الوحدة',
        'quantity'   => 'الكمية',
        'price'      => 'السعر',
        'total'      => 'الاجمالي',
        // endregion: contractor_project
    ],
    'statuses' => [
        1 => 'مفعل',
        2 => 'معطل',
    ],
];
