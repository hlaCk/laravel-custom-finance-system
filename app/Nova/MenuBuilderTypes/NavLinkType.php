<?php

namespace App\Nova\MenuBuilderTypes;

use App\Models\Info\Client;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Text;

/**
 * Nova menu type
 * todo: on create add other languages
 */
class NavLinkType extends FrontendType
{
    public static function getIdentifier(): string
    {
        return 'Nav-links';
    }

    /**
     * Get menu link name shown in CMS when selecting link type.
     * ie ('Product Link' or 'Image Link').
     *
     * @return string
     **/
    public static function getName(): string
    {
        return (string) __('common.menu_types.NavLink.NAME');
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
            Text::make(__('common.menu_types.NavLink.TITLE', compact('locale')), 'title'),

            Boolean::make(__('common.menu_types.NavLink.has_children'), 'has_children'),
        ];
    }

    /**
     * Get list of options shown in a select dropdown.
     *
     * Should be a map of [key => value, ...], where key is a unique identifier
     * and value is the displayed string.
     *
     * @return array
     **/
    public static function getOptions($locale): array
    {
        return [
            'races' => Client::trans('plural'),
        ];
    }

    public static function getRules(): array
    {
        return [
            'title' => [ 'required', 'string' ],
            'value' => [ 'required', 'string', 'in:' . implode(',', array_keys(static::getOptions(request('locale')))) ],
        ];
    }
}
