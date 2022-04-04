<?php

namespace App\Nova\MenuBuilderTypes;

use Laravel\Nova\Fields\Text;

/**
 *
 */
class MenuItemStaticURLType extends \OptimistDigital\MenuBuilder\MenuItemTypes\MenuItemStaticURLType
{
    public static function getIdentifier(): string
    {
        return 'Static URL';
    }

    public static function getName(): string
    {
        return (string) __('common.menu_types.MenuItemStaticURL.NAME');
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
            Text::make(__('common.menu_types.MenuItemStaticURL.TITLE', compact('locale')), 'title'),
        ];
    }

}
