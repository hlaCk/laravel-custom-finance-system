<?php

return [
    /**
     * Sort menu order.
     */
    'sort'              => [
        "Chart",
        "Info",
        "Contractors",
        "Project",
        "Sheet",
        "Settings",
        "Administration",
        "Reports",
    ],

    /**
     * Hidden menus.
     */
    'hidden'            => [
        // "الدعم",
    ],

    /**
     * Toggle hidden menus by keyboard.
     */
    'toggle_hidden_key' => [
        "shift"  => true,
        "code"   => "Space",
        "repeat" => 2, // repeater count to trigger the toggle
    ],

    // for quick search
    'others'            => [
        'Administration' => [
            $menu_builder = [
                'active'          => false,
                'group'           => $menu_group = 'Administration',
                'navigationLabel' => 'novaMenuBuilder.sidebarTitle',
                'uriKey'          => \OptimistDigital\MenuBuilder\MenuBuilder::getMenuResource()::uriKey(),
            ],
            $translation_editor = [
                'active'          => false,
                'group'           => $translation_editor_group = 'Administration',
                'navigationLabel' => 'Translation Editor',
                'uriKey'          => config('app.nova.path') . '/nova-translation-editor',
            ],
        ],
        'Reports'        => [
            [
                'active'          => true,
                'group'           => 'Reports',
                'navigationLabel' => 'CompanyReportYTD',
                'uriKey'          => config('app.nova.path') . '/company-report-y-t-d',
                'name'            => 'company-report-y-t-d',
            ],
            [
                'active'          => true,
                'group'           => 'Reports',
                'navigationLabel' => 'ProjectsReportYTD',
                'uriKey'          => config('app.nova.path') . '/projects-report-y-t-d',
                'name'            => 'projects-report-y-t-d',
            ],
        ],
    ],

    'menu_builder'       => $menu_builder,
    'translation_editor' => $translation_editor,
];
