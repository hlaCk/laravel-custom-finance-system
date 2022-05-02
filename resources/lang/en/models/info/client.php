<?php

return [
    'plural' => 'Clients',
    'singular' => 'Client',
    'fields' => [
        'name' => 'Name',
        'type' => "Type",
        'status' => 'Status',
        'projects' => 'Projects',
        'created_at' => 'Created at',
        'updated_at' => 'Updated at',
        'deleted_at' => 'Deleted at',
    ],
    'statuses' => [
        1 => 'Active',
        2 => 'Inactive',
    ],
    'types' => [
        'cash' => 'Cash',
        'credit' => 'Credit',
    ],

];
