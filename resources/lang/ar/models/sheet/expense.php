<?php

return [
    'plural'                           => 'المصروفات',
    'singular'                         => 'مصروفات',
    'fields'                           => [
        'date'           => 'التاريخ',
        'amount'         => 'المبلغ',
        'vat_included'   => 'المبلغ شامل الضريبة',
        'remarks'        => 'الملاحظات',
        'status'         => 'الحالة',
        'project'        => 'المشروع',
        'entry_category' => 'التصنيف',
        'created_at'     => 'تاريخ الانشاء',
        'updated_at'     => 'تاريخ التعديل',
        'deleted_at'     => 'تاريخ الحذف',
    ],
    'statuses'                         => [
        1 => 'جديد',
        2 => 'منتهي',
    ],
    'projects_report_ytd_header_label' => 'تصنيف المصروفات',
    'company_report_ytd_header_label'  => 'ملخص',
];
