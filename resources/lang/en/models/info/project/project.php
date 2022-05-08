<?php

return [
    'plural' => 'Projects',
    'singular' => 'Project',
    'fields' => [
        'name' => 'Name',
        'base_cost' => 'Base Cost',
        'cost' => 'Cost',
        'costs' => 'Cost',
        'project_status' => 'Project Status',
        'status' => 'Status',
        'project_costs' => 'Project Costs',
        'client' => 'Client',
        'contractors' => 'Contractors',
        'credits' => 'Credits',
        'expenses' => 'Expenses',
        'created_at' => 'Created at',
        'updated_at' => 'Updated at',
        'deleted_at' => 'Deleted at',

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
