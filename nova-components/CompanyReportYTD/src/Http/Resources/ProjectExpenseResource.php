<?php

namespace Sheets\CompanyReportYTD\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProjectExpenseResource extends JsonResource
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
        /** @var \App\Models\Sheet\Expense $model */
        $model = $this->resource;
        $only = [
            'id',
            'date',
            'amount',
            'vat_included',
            'remarks',
            'status',
            'entry_category_name',
        ];

        return $model->only(
            array_values(array_except(array_combine($only, $only), $this->except))
        );
    }
}
