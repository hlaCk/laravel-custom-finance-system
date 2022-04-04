<?php

return [
    'plural'   => 'Chart Accounts',
    'singular' => 'Chart Account',
    'fields'   => [
        'name'           => 'Name',
        'type'           => "Type",
        'status'         => 'Status',
        'account'        => 'Account',
        'parent_account' => 'Parent Account',
        'sub_accounts'   => 'Child Accounts',
        'created_at'     => 'Created at',
        'updated_at'     => 'Updated at',
        'deleted_at'     => 'Deleted at',
    ],
    'statuses' => [
        1 => 'Active',
        2 => 'Inactive',
    ],
    'types'    => [
        'budget' => 'Budget',
        'pnl'    => 'PNL',
    ],

];
