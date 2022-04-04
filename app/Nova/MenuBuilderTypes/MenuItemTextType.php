<?php

namespace App\Nova\MenuBuilderTypes;

use Laravel\Nova\Fields\Text;

/**
 *
 */
class MenuItemTextType extends \OptimistDigital\MenuBuilder\MenuItemTypes\MenuItemTextType
{
    public static function getIdentifier(): string
    {
        return 'Static Text';
    }

    public static function getName(): string
    {
        return (string) __('common.menu_types.MenuItemText.NAME');
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array An array of fields.
     */
    public static function getFields(): array
    {
        $requestLocale = currentLocale();
        $locale = getTrans("common.locales.{$requestLocale}", '');

        return [
            Text::make(__('common.menu_types.MenuItemText.TITLE', compact('locale')), 'title'),
        ];
    }

    public static function getRules(): array
    {
        return [
            'title' => [ 'required', 'string' ],
        ];
    }
}
