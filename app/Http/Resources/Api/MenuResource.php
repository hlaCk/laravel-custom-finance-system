<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\Api\Abstracts\AbstractJsonResource;

class MenuResource extends AbstractJsonResource
{
    protected ?string $_locale;

    public function __construct($resource, ?string $locale = null)
    {
        parent::__construct($resource);
        $this->_locale = $locale;
    }

    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var \OptimistDigital\MenuBuilder\Models\Menu $model */
        $model = $this->resource;
        $locale = $this->_locale ?? $model->locale;

        return [
            'id' => $model->id,
            'name' => $model->name,
            'slug' => $model->slug,
            'locale' => $locale,
            'menuItems' => MenuItemCollectionResource::collection(
                $model->rootMenuItems()
                      ->where('locale', $locale)
                      ->get(),
                $locale
            )->toArray($request),
        ];
    }
}
