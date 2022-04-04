<?php

namespace App\Nova\MenuBuilderTypes;

use Laravel\Nova\Fields\Text;

/**
 * Nova menu type
 * todo: on create add other languages
 */
class StaticDropdownType extends FrontendType
{
    public static function getType(): string
    {
        return '';
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
            Text::make(__('common.menu_types.StaticDropdown.TITLE', compact('locale')), 'title'),
        ];
    }

    public static function getIdentifier(): string
    {
        return 'Static Dropdown';
    }

    /**
     * Get menu link name shown in CMS when selecting link type.
     * ie ('Product Link' or 'Image Link').
     *
     * @return string
     **/
    public static function getName(): string
    {
        return (string) __('common.menu_types.StaticDropdown.NAME');
    }

    public static function getRules(): array
    {
        return [
            'title' => [ 'required', 'string' ],
        ];
    }
}
