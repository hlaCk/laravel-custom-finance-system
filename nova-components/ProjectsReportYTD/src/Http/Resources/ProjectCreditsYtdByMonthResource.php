<?php

namespace Sheets\ProjectsReportYTD\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectCreditsYtdByMonthResource extends JsonResource
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
        $grand_total = $model->pull('grand_total') ?? $model->map->sum('value');

        return [
            'data'  => $model,
            'grand_total' => $grand_total
        ];
    }
}
