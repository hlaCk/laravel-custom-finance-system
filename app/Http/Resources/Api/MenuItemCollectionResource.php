<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 *
 */
class MenuItemCollectionResource extends JsonResource
{
    protected ?string $_locale;

    /**
     * Create a new resource instance.
     *
     * @param  mixed  $resource
     * @return void
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
        $this->_locale = $this->locale;
    }

    /**
     * Transform the resource collection into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var \OptimistDigital\MenuBuilder\Models\MenuItem $menu */
        $menu = $this->resource;

        $locale = $this->_locale ?? $menu->locale;
        $customData = $menu->customData;
        $route = $menu->value;
        $target = $menu->target;
        $title = array_pull($customData, 'title');
        $children = null;

        if( $menu->class === \App\Nova\MenuBuilderTypes\DynamicDropdownType::class ) {
            $route = array_pull($customData, 'source');
            $target = null;
        }

        if( $menu->class === \App\Nova\MenuBuilderTypes\StaticDropdownType::class ) {
            $route = null;
            $target = null;
            $children = MenuItemCollectionResource::collection(
                $menu->children()
                     ->enabled()
                     ->byLocale($locale)
                     ->get()
            );
        }

        if( $menu->class === \App\Nova\MenuBuilderTypes\MenuItemTextType::class ) {
            $target = null;
        }

        return [
            'id' => $menu->id,
            'type' => $menu->type,

            $this->mergeWhen(!is_null($route), compact('route')),
            'title' => $title,
            $this->mergeWhen(!is_null($target), compact('target')),
            $this->mergeWhen(!is_null($children), compact('children')),

            $this->mergeWhen(!empty($customData), [ 'data' => $customData ]),
        ];
    }
}

