<?php

namespace App\Nova\MenuBuilderTypes;

use App\Nova\Abstracts\Resource;
use Laravel\Nova\Fields\Text;
use OptimistDigital\MenuBuilder\MenuItemTypes\MenuItemSelectType;

/**
 *
 */
abstract class FrontendType extends MenuItemSelectType
{
    /**
     * @var string|\App\Nova\Abstracts\Resource
     */
    public static string $resource = "";

    /**
     * @param \Closure|null $then
     *
     * @return mixed
     */
    public static function getResourceModel(?\Closure $then = null)
    {
        $then = $then ?? fn($a) => $a;

        return $then(static::getResource(fn($r) => $r::$model));
    }

    /**
     * @param \Closure|null $then
     *
     * @return mixed|null
     */
    public static function getResource(?\Closure $then = null)
    {
        $then = $then ?? fn($a) => $a;

        return static::$resource && class_exists(static::$resource) ? $then(static::$resource) : null;
    }

    public static function getIdentifier(): string
    {
        return nova_menu_builder_sanitize_panel_name(static::getResource(fn($r) => $r::label()));
    }

    /**
     * Get menu link name shown in CMS when selecting link type.
     * ie ('Product Link' or 'Image Link').
     *
     * @return string
     **/
    public static function getName(): string
    {
        return (string)static::getResource(fn($r) => $r::label());
    }

    public static function getType(): string
    {
        return 'select';
    }

    /**
     * Get fields for locale.
     *
     * @param \Closure|string      $name
     * @param \Closure|string|null $title
     *
     * @return array An array of fields.
     */
    public static function getLocalizedField($name, $title = null, ?array $locales = null): array
    {
        $name = value($name);
        $title = value($title) ?: static::getResourceModel(fn($m) => $m::trans($name));
        $locales = is_null($locales) ? config('nova-menu.locales', []) : $locales;

        $fields = [];
        foreach( (array) $locales as $locale => $locale_name ) {
            $fields[] = Text::make("{$locale_name} {$title}", "{$name}->{$locale}");
        }

        return $fields;
    }
}
