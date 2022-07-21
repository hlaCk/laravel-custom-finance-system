<?php

namespace App\Http\Resources\Api\Abstracts;

class SelectOptionsResource extends AbstractJsonResource
{
    /**
     * Create a new anonymous resource collection.
     *
     * @param \Illuminate\Support\Collection|\Illuminate\Http\Resources\MissingValue|array|mixed $resources
     * @param string|\Closure                                                                    $labelKey
     * @param string|\Closure                                                                    $valueKey
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function collection($resources, $labelKey = 'name', $valueKey = 'id')
    {
        try {
            $resources = isBuilder($resources) ? $resources->get([$valueKey, $labelKey]) : $resources;

        } catch (\Exception $exception) {
            dE($resources->toSql(),[$valueKey, $labelKey],$exception->getMessage());
        }
        foreach( $resources as &$resource ) {
            $label = $labelKey instanceof \Closure ? $labelKey($resource) : $resource[ $labelKey ];
            $value = $valueKey instanceof \Closure ? $valueKey($resource) : $resource[ $valueKey ];
            /** @var iterable $resource */
            $resource[ '_select_options' ] = [
                'value' => $value,
                'label' => $label,
            ];
        }
        unset($resource);
        return parent::collection($resources);
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
        /** @var \App\Models\Info\Project\Project $model */
        $model = $this->resource;
        return $model['_select_options'] ?? parent::toArray($request);
    }
}
