<?php

namespace App\Http\Resources\Api\Abstracts;

class ModelIdsResource extends AbstractJsonResource
{
    public $id_key;

    public function __construct($resource, $id_key = 'id') {
        $this->id_key = $id_key;
        parent::__construct($resource);
    }

    /**
     * Create a new anonymous resource collection.
     *
     * @param \Illuminate\Support\Collection|\Illuminate\Http\Resources\MissingValue|array|mixed $resources
     * @param string|\Closure                                                                    $labelKey
     * @param string|\Closure                                                                    $valueKey
     *
     * @return \Illuminate\Http\Resources\Json\AnonymousResourceCollection
     */
    public static function collection($resources, $idKey = 'id')
    {
        $resources = isBuilder( $resources) ? $resources->get([ $idKey ]) : $resources;
        foreach( $resources as &$resource ) {
            /** @var iterable $resource */
            $resource[ 'id_key' ] = $idKey;
            $resource[ '_select_options' ] = $resource[ $idKey ];
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
        if( isset($model[ 'id_key' ]) ) {
            $this->id_key = $model[ 'id_key' ];
        }
        return $model[ '_select_options' ] ?? data_get($model, value($this->id_key));
    }
}
