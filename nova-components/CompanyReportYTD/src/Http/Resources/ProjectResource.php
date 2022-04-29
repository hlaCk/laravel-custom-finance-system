<?php

namespace Sheets\CompanyReportYTD\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    protected array $except;

    /**
     * @param       $resource
     * @param array $except
     */
    public function __construct($resource, array $except = [])
    {
        $this->except($except);
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
        /** @var \App\Models\Info\Project\Project $model */
        $model = $this->resource;
        $only = [
            'id',
            'cost',
            'cost_label',
            'name',
            'project_status_name',
            'status_label',
            'credit_total',
            'credit_total_label',
            'credit_count',
            'expenses_total',
            'expenses_total_label',
            'balance',
            'balance_label',
            'remaining',
            'remaining_label',
        ];

        return $model->only(
            array_values(array_except(array_combine($only, $only), $this->except))
        );
    }
}
