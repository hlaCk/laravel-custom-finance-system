<?php

return [
    'plural'                                     => 'Expenses',
    'singular'                                   => 'Expenses',
    'fields'                                     => [
        'date'           => 'Date',
        'amount'         => 'Amount',
        'vat_included'   => 'Amount includes Vat',
        'remarks'        => 'Remarks',
        'status'         => 'Status',
        'project'        => 'Project',
        'entry_category' => 'Category',
        'contractor'     => 'Contractor',
        'created_at'     => 'Created at',
        'updated_at'     => 'Updated at',
        'deleted_at'     => 'Deleted at',
    ],
    'statuses'                                   => [
        1 => 'New',
        2 => 'Expired',
    ],
    'projects_report_ytd_header_label'           => 'Expenses category',
    'company_report_ytd_header_label'            => 'Summary',
    'company_report_ytd_by_project_header_label' => 'Expenses By Project',
];
