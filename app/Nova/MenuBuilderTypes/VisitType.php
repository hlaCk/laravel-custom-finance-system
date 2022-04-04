<?php

namespace App\Nova\MenuBuilderTypes;

use App\Nova\Info\Client as TModel;

/**
 * Nova menu type
 */
class VisitType extends FrontendType
{
    /** @var string|\App\Nova\Abstracts\Resource */
    public static string $resource = TModel::class;

    /**
     * Get the subtitle value shown in CMS menu items list.
     *
     * @param            $value
     * @param array|null $data The data from item fields.
     * @param            $locale
     *
     * @return string
     */
    public static function getDisplayValue($value, ?array $data, $locale)
    {
        $label = static::getResource(fn($r) => $r::singularLabel()) ?: "";
        $title = static::getResource(fn($r) => $r::$title) ?: 'id';
        $value =
            "{$label}: " .
            ($value == 0 ? __('All') :
                static::getResourceModel(
                    function($c) use ($title, $value, $label) {
                        $v = $c::find($value);

                        return "{$v[$title]}";
                    }
                ));
        $titles = data_get($data, 'title', []);
        $titles = [ data_get($titles, 'ar'), data_get($titles, 'en') ];
        $titles = implode(" - ", $titles);

        return ($value ? "{$value} | " : "") . $titles;
    }

    /**
     * Get the fields displayed by the resource.
     *
     * @return array An array of fields.
     */
    public static function getFields(): array
    {
        return static::getLocalizedField('title', __('Title'));
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
        $title = static::getResource(fn($r) => $r::$title);

        return toCollect(
            static::getResourceModel(
                fn($c) => $c::get([ $title, 'id' ])->mapWithKeys(fn($v, $k) => [ $v[ 'id' ] => "{$v['id']} - {$v[$title]}" ])
            )
        )->prepend(__('All'))->toArray();
    }

    public static function getRules(): array
    {
        return [];
    }
}
