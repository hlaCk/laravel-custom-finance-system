<?php

return [
    /**
     * Sort menu order.
     */
    'sort'              => [
        "Chart",
        "Info",
        "Project",
        "Sheet",
        "Settings",
        "Administration",
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
        'Administration'          => [
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
    ],

    'menu_builder'       => $menu_builder,
    'translation_editor' => $translation_editor,
];