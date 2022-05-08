<?php

return [
    'plural' => 'مقاولين',
    'singular' => 'مقاول',
    'fields' => [
        'name' => 'الاسم',
        'contractor_speciality' => 'المجال',
        'projects' => 'المشاريع',
        'status' => 'الحالة',
        'created_at' => 'تاريخ الانشاء',
        'updated_at' => 'تاريخ التعديل',

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
