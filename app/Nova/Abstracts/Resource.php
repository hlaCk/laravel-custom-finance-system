<?php

namespace App\Nova\Abstracts;

use App\Nova\Packages\SearchRelations\SearchesRelations;
use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Panel;
use Laravel\Nova\Resource as NovaResource;

/**
 * @property \App\Models\Abstracts\Model $model
 */
abstract class Resource extends NovaResource
{
    use SearchesRelations;
    use \OptimistDigital\NovaTranslatable\HandlesTranslatable;

    /**
     * The per-page options used the resource index.
     *
     * @var array
     */
    public static $perPageOptions = [25, 50, 100, 250, 500, 1000];

    /**
     * The number of resources to show per page via relationships.
     *
     * @var int
     */
    public static $perPageViaRelationship = 10;

    /**
     * Navigation icon preference.
     *
     * @var array|string[]
     */
    private static array $navigationIconPreference = [
        'color' => 'var(--sidebar-icon)',
        'icon' => 'icon-hashtag',
    ];
    /**
     * blade file name for icon.
     *
     * @var string
     */
    private static string $iconViewName = "icon";
    /**
     * blade file name for icon.
     *
     * @var string
     */
    private static string $icon = "<img src='/images/circle_copy.svg' class='inner-nav-icon'>";

    /**
     * Reset order by.
     *
     * @example [[ 'id', 'asc' ]]|[[ 'id' ], [ 'name', 'asc' ]]|'id'|null
     *
     * @var string|array|null
     */
    public static $order_by = null;

    /**
     * Build an "index" query for the given resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \Illuminate\Database\Eloquent\Builder   $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function indexQuery(NovaRequest $request, $query)
    {
        $query = parent::indexQuery($request, $query);

        if( !is_null($orders = static::$order_by) ) {
            $orders = is_array($orders) ? $orders : [ [ $orders ] ];
            $query->getQuery()->orders = [];

            foreach( $orders as $order ) {
                $order = array_filter(is_array($order) ? $order : [ $order ], fn($v) => $v);
                $query = $query->orderBy(...$order);
            }
        }

        return $query;
    }

    /**
     * Build a Scout search query for the given resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \Laravel\Scout\Builder                  $query
     *
     * @return \Laravel\Scout\Builder
     */
    public static function scoutQuery(NovaRequest $request, $query)
    {
        return $query;
    }

    /**
     * Build a "detail" query for the given resource.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \Illuminate\Database\Eloquent\Builder   $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function detailQuery(NovaRequest $request, $query)
    {
        return parent::detailQuery($request, $query);
    }

    /**
     * Build a "relatable" query for the given resource.
     * This query determines which instances of the model may be attached to other resources.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \Illuminate\Database\Eloquent\Builder   $query
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public static function relatableQuery(NovaRequest $request, $query)
    {
        return parent::relatableQuery($request, $query);
    }

    /**
     * Check if the current resource equal to this resource.
     *
     * @return bool
     */
    public static function isCurrent(): bool
    {
        return ($currentResource = request('view')) &&
            method_exists(static::class, 'uriKey') &&
            $currentResource === 'resources/' . static::uriKey();
    }

    /**
     * Determine if this resource is available for navigation.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return bool
     */
    public static function availableForNavigation(Request $request)
    {
        $policy = policy(static::$model);
        if( $policy && method_exists($policy, 'availableForNavigation') ) {
            return $policy->availableForNavigation();
        } else {
            return static::$displayInNavigation;
        }
    }

    /**
     * Get the logical group associated with the resource.
     *
     * @return string
     */
    public static function group()
    {
        return __(static::$group);
    }

    /**
     * Get the displayable label of navigation.
     *
     * @return string
     */
    public static function navigationLabel(): string
    {
        return static::label();
    }

    /**
     * Get the displayable label of the resource.
     *
     * @return string
     */
    public static function label()
    {
        $model = snake_case(/*\Str::singular*/ (class_basename(static::$model)));

        return (string) static::$model::trans('plural');
        return (string) __("models/{$model}.plural");
    }

    /**
     * Get the displayable singular label of the resource.
     *
     * @return string
     */
    public static function singularLabel()
    {
        $singularLabel = parent::singularLabel();
        $model = snake_case(/*\Str::singular*/ (class_basename(static::$model)));

        return (string) $singularLabel !== static::label() ? $singularLabel : static::$model::trans('singular');
        return (string) $singularLabel !== static::label() ? $singularLabel : __("models/{$model}.singular");
    }

    /**
     * @return string
     */
    public static function icon()
    {
        $icon = static::$icon ?? null;
        return $icon ?: view(static::$iconViewName, static::navigationIconPreference())->render();
    }

    /**
     * Navigation icon preference setter/getter.
     *
     * @param string|array|int|null $key
     * @param mixed|null            $value
     *
     * @return array|string|mixed|null
     */
    public static function navigationIconPreference($key = null, $value = null)
    {
        static::$navigationIconPreference[ 'color' ] ??= 'currentColor';
        static::$navigationIconPreference[ 'icon' ] ??= 'icon-hashtag';

        if( !func_num_args() ) {
            return static::$navigationIconPreference;
        }

        if( func_num_args() === 1 ) {
            if( is_array($key) ) {
                return static::$navigationIconPreference = array_merge(static::$navigationIconPreference, $key);
            }

            return static::$navigationIconPreference[ $key ] ?? null;
        }

        return static::$navigationIconPreference[ $key ] = $value;
    }

    /**
     * @param bool                                                                           $wrap_function wrap the result with closure
     * @param null|\Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model $query
     *
     * @return array|\Closure
     */
    public function getOrderDropDownOptions(bool $wrap_function = false, $query = null)
    {
        $query ??= $this::$model::query();
        $result = fn() => getOrderDropDownOptions($this, $query);

        return $wrap_function ? $result : $result();
    }

    /**
     * Get the panels that are available for the given detail request.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \Laravel\Nova\Resource                  $resource
     *
     * @return array
     */
    public function availablePanelsForDetail(\Laravel\Nova\Http\Requests\NovaRequest $request, \Laravel\Nova\Resource $resource)
    {
        return $this->panelsWithDefaultLabel(static::defaultNameForDetail($resource), $request);
    }

    /**
     * Get the panels that are available for the given update request.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \Laravel\Nova\Resource                  $resource
     *
     * @return array
     * @todo add custom method
     */
    public function availablePanelsForUpdate(\Laravel\Nova\Http\Requests\NovaRequest $request, \Laravel\Nova\Resource $resource = null)
    {
        return $this->panelsWithDefaultLabel(Panel::defaultNameForUpdate($resource ?? $request->newResource()), $request);
    }

    /**
     * Get the default panel name for the given resource.
     *
     * @param \Laravel\Nova\Resource $resource
     *
     * @return string
     */
    public static function defaultNameForDetail(\Laravel\Nova\Resource $resource)
    {
        return is_callable([static::$model, 'getCustomTitle']) ?
            call_user_func_array([static::$model, 'getCustomTitle'], [[
                                                                          'label' => [ 'titles', $resource->type ],
                                                                          'title' => $resource,
                                                                          'default' => Panel::defaultNameForDetail($resource),
                                                                          'title_key' => 'details_title_template',
                                                                      ]]) :
            Panel::defaultNameForDetail($resource);
    }

    /**
     * Resolve the detail fields and assign them to their associated panel.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \Laravel\Nova\Resource                  $resource
     *
     * @return \Laravel\Nova\Fields\FieldCollection
     */
    public function detailFieldsWithinPanels(\Laravel\Nova\Http\Requests\NovaRequest $request, \Laravel\Nova\Resource $resource)
    {
        return $this->assignToPanels(
            static::defaultNameForDetail($resource ?? $request->newResource()),
            $this->detailFields($request)
        );
    }
}
