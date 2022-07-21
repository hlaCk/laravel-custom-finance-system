<?php

namespace App\Http\Resources\Api\Abstracts;

abstract class DataByProjectBTableResource extends AbstractJsonResource
{

    /**
     * @param       $resource
     */
    public function __construct($resource)
    {
        parent::__construct($resource);
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
        /** @var \Illuminate\Support\Collection $model */
        $model = toCollect($this->resource);
        $headers = $model->pull('headers', []);
        $credits = $model->pull('data', []);

        return [
            'headers' => $headers,
            'payload' => $credits,
        ];
    }
}
