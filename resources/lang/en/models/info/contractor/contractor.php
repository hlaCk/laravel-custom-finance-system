<?php

return [
    'plural' => 'Contractors',
    'singular' => 'Contractor',
    'fields' => [
        'name' => 'Name',
        'contractor_speciality' => 'Speciality',
        'projects' => 'Projects',
        'status' => 'Status',
        'created_at' => 'Created at',
        'updated_at' => 'Updated at',

        // region: contractor_project
        'date'       => 'Date',
        'remarks'    => 'Statement',
        'unit'       => 'Unit',
        'quantity'   => 'Quantity',
        'price'      => 'Price',
        'total'      => 'Total',
        // endregion: contractor_project
    ],
    'statuses' => [
        1 => 'Active',
        2 => 'Inactive',
    ],
];
