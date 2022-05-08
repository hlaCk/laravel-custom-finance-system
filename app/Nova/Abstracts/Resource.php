<?php

namespace App\Nova\Abstracts;

use App\Nova\Packages\SearchRelations\SearchesRelations;
use Illuminate\Http\Request;
use Laravel\Nova\Http\Requests\NovaRequest;
use Laravel\Nova\Makeable;
use Laravel\Nova\Panel;
use Laravel\Nova\Resource as NovaResource;

/**
 * @property \App\Models\Abstracts\Model $model
 */
abstract class Resource extends NovaResource
{
    use Makeable;
    use SearchesRelations;
    use \OptimistDigital\NovaTranslatable\HandlesTranslatable;

    /**
     * The per-page options used the resource index.
     *
     * @var array
     */
    public static $perPageOptions = [ 25, 50, 100, 250, 500, 1000 ];

    /**
     * The number of resources to show per page via relationships.
     *
     * @var int
     */
    public static $perPageViaRelationship = 10;
    /**
     * Reset order by.
     *
     * @example [[ 'id', 'asc' ]]|[[ 'id' ], [ 'name', 'asc' ]]|'id'|null
     *
     * @var string|array|null
     */
    public static $order_by = null;
    /**
     * To make the resource ($globallySearchable = false, $tableStyle = tight)
     *
     * @var bool
     */
    public static bool $partialResource = false;
    /**
     * The columns that should be hidden on relationship index view.
     *
     * @var array
     */
    public static $hideAttributesFromRelationshipsIndex = [];
    /**
     * Navigation icon preference.
     *
     * @var array|string[]
     */
    private static array $navigationIconPreference = [
        'color' => 'var(--sidebar-icon)',
        'icon'  => 'icon-hashtag',
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
     * alias for __("models/model_name") and __("models/model_name.fields.field_name")
     *
     * @param string $key
     *
     * @return array|string|null
     */
    public static function trans(string $key, $replace = [], $locale = null)
    {
        return static::$model::trans($key, $replace, $locale);
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
        if( ($policy = policy(static::$model)) && method_exists($policy, 'availableForNavigation') ) {
            return $policy->availableForNavigation($request);
        }

        return static::$displayInNavigation;
    }

    /**
     * Get the panels that are available for the given detail request.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \Laravel\Nova\Resource                  $resource
     *
     * @return array
     */
    public function availablePanelsForDetail(
        \Laravel\Nova\Http\Requests\NovaRequest $request,
        \Laravel\Nova\Resource                  $resource
    ) {
        return $this->panelsWithDefaultLabel(static::defaultNameForDetail($resource), $request);
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
        return is_callable([ static::$model, 'getCustomTitle' ])
            ?
            call_user_func_array([ static::$model, 'getCustomTitle' ], [
                [
                    'label'     => [ 'titles', $resource->type ],
                    'title'     => $resource,
                    'default'   => Panel::defaultNameForDetail($resource),
                    'title_key' => 'details_title_template',
                ],
            ])
            :
            Panel::defaultNameForDetail($resource);
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
    public function availablePanelsForUpdate(
        \Laravel\Nova\Http\Requests\NovaRequest $request,
        \Laravel\Nova\Resource                  $resource = null
    ) {
        return $this->panelsWithDefaultLabel(
            Panel::defaultNameForUpdate($resource ?? $request->newResource()),
            $request
        );
    }

    /**
     * Resolve the detail fields and assign them to their associated panel.
     *
     * @param \Laravel\Nova\Http\Requests\NovaRequest $request
     * @param \Laravel\Nova\Resource                  $resource
     *
     * @return \Laravel\Nova\Fields\FieldCollection
     */
    public function detailFieldsWithinPanels(
        \Laravel\Nova\Http\Requests\NovaRequest $request,
        \Laravel\Nova\Resource                  $resource
    ) {
        return $this->assignToPanels(
            static::defaultNameForDetail($resource ?? $request->newResource()),
            $this->detailFields($request)
        );
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
     * Get the logical group associated with the resource.
     *
     * @return string
     */
    public static function group()
    {
        return __(static::$group);
    }

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
     * Determine if is the resource available in global search.
     *
     * @return bool
     */
    public static function isGloballySearchable()
    {
        return !static::isPartialResource() && static::$globallySearchable;
    }

    /**
     * Determine if is the resource available in global search.
     *
     * @return bool
     */
    public static function isPartialResource()
    {
        return (bool) static::$partialResource;
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
     * Get the visual style that should be used for the table.
     *
     * @return string
     */
    public static function tableStyle()
    {
        return static::isPartialResource() ? 'tight' : static::$tableStyle;
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
     * Merge a value based on a current resource if its match this resource.
     *
     * @param array|mixed $attributes
     *
     * @return \Illuminate\Http\Resources\MergeValue|mixed
     */
    public function mergeWhenCurrentResourceIsSelf($attributes = [])
    {
        return $this->mergeWhenCurrentResourceIs(static::class, $attributes);
    }

    /**
     * Merge a value based on a given resource if its match current resource.
     *
     * @param string|\App\Nova\Abstracts\Resource|mixed $resource
     * @param array|mixed                               $attributes
     *
     * @return \Illuminate\Http\Resources\MergeValue|mixed
     */
    public function mergeWhenCurrentResourceIs($resource, $attributes = [])
    {
        $resource = value($resource);
        $resource = !is_string($resource) ? get_class($resource) : $resource;

        return $this->mergeWhen(isCurrentResource($resource), $attributes);
    }

    public function parseFields(Request $request, $grouped_fields)
    {
        $fields = [];
        foreach( $grouped_fields as $index => $_fields ) {
            $_fields = array_wrap($_fields);
            if( is_numeric($index) ) {
                $fields = array_merge($fields, $_fields);
            } else {
                if( is_string($index) ) {
                    $_args = explode(",", $index);
                    $index = array_shift($_args);
                    $_args[] = $_fields;

                    $parseFields = method_exists($this, $index) ? $this->{$index}(...$_args) : null;
                    $parseFields ??= is_callable($index) ? $index(...$_args) : $_fields;
                    $fields[] = $parseFields;
                }
            }
        }

        return array_merge($fields, static::getFieldsForRelationships($request));
    }

    /**
     * Get prepared fields displayed by the resource in relationships.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public static function getFieldsForRelationships(Request $request, ?bool $for_pivot = null): array
    {
        $for_pivot ??= $request->viaRelationship();
        $hideFromRelationshipsIndex = static::$hideAttributesFromRelationshipsIndex;
        $applyHideFromRelationshipsIndex =
            function ($m) use ($hideFromRelationshipsIndex) {
                if( !empty($hideFromRelationshipsIndex) ) {
                    return $m->{in_array($m->attribute, $hideFromRelationshipsIndex)
                        ? 'hideFromRelationships'
                        : 'showOnRelationships'}();
                }

                return $m;
            };

        $fields = collect(static::rawFieldsForRelationships($request, $for_pivot))
            ->when(!empty($hideFromRelationshipsIndex), fn($e) => $e->each($applyHideFromRelationshipsIndex));

        return $fields->all();
    }

    /**
     * Get the fields displayed by the resource in relationships.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array
     */
    public static function rawFieldsForRelationships(Request $request, ?bool $for_pivot = null): array
    {
        $for_pivot ??= $request->viaRelationship();
        return [];
    }
}
