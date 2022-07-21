<?php

namespace App\Http\Resources\Api\Info;

use App\Http\Resources\Api\Abstracts\AbstractJsonResource;

class EntryCategoryResource extends AbstractJsonResource
{
    protected array $except;

    /**
     * @param       $resource
     */
    public function __construct($resource)
    {
        $this->except();
        parent::__construct($resource);
    }

    public function except(array $except = [])
    {
        $this->except = $except;
        return $this;
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
        /** @var \App\Models\Info\EntryCategory $model */
        $model = $this->resource;
        $only = [
            'id',
            'name',
            'status',
            'has_contractor',
        ];
        $this->except =
            empty($this->except) ? array_except(array_combine($only, $only), array_keys($model->toArray())) : $this->except;

        return $model->only(
            array_values(array_except(array_combine($only, $only), $this->except))
        );
    }
}
