<?php

namespace App\Nova\MenuBuilderTypes;

use App\Models\Info\Client;
use Epartment\NovaDependencyContainer\NovaDependencyContainer;
use Laravel\Nova\Fields\Boolean;
use Laravel\Nova\Fields\Select;
use Laravel\Nova\Fields\Text;
use Laravel\Nova\Http\Requests\NovaRequest;
use Orlyapps\NovaBelongsToDepend\NovaBelongsToDepend;

/**
 * Nova menu type
 * todo: on create add other languages
 */
class DynamicDropdownType extends FrontendType
{
    public static function getOptionClass($option = null, \Closure $cb = null)
    {
        $cb = $cb ?? fn($m) => $m;
        $classes = [
            'client' => Client::class,
        ];

        $class = is_null($option) ? $classes : data_get($classes, $option);

        return $class ? $cb($class::make()) : null;
    }

    public static function getType(): string
    {
        return '';
    }

    public static function getIdentifier(): string
    {
        return 'Dynamic Dropdown';
    }

    /**
     * Get menu link name shown in CMS when selecting link type.
     * ie ('Product Link' or 'Image Link').
     *
     * @return string
     **/
    public static function getName(): string
    {
        return (string) __('common.menu_types.DynamicDropdown.NAME');
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
            Select::make(__('Source'), 'source')
                  ->options(static::getOptions($requestLocale))
                  ->displayUsingLabels(),

            Text::make(__('common.menu_types.DynamicDropdown.TITLE', compact('locale')), 'title'),
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
            'client' => __('common.menu_types.DynamicDropdown.client'),
        ];
    }

    public static function getRules(): array
    {
        return [
            'title' => [ 'required', 'string' ],
        ];
    }
}
