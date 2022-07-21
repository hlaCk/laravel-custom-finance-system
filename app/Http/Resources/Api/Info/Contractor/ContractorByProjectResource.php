<?php

namespace App\Http\Resources\Api\Info\Contractor;

use App\Http\Resources\Api\Abstracts\AbstractJsonResource;
use App\Http\Resources\Api\Abstracts\SelectOptionsResource;
use App\Models\Info\Contractor\Contractor;

class ContractorByProjectResource extends AbstractJsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        /** @var \App\Models\Info\Project\Project $contractors */
        $project = $this->resource;

//        $contractors = $project->contractors
//            ->mapWithKeys(function ($contractors, $key) {
//                $project_id = 0;
//                $contractors->first(function ($c) use (&$project_id) {
//                    return $project_id = optional($c->pivot)->project_id;
//                });
//
//                $contractors = $contractors->map(
//                    fn(Contractor $contractor) => [ "value" => $contractor->id, "label" => $contractor->name ]
//                );
//                return compact('contractors', 'project_id');
//            });
        return [
            'data' => SelectOptionsResource::collection($project->contractors),
            'project_id' => $project->id,
        ];
        dd($contractors);
        $data = $contractors->each->mapWithKeys()
                                  ->toArray();
//        dd($data);
        return $data;
    }
}
