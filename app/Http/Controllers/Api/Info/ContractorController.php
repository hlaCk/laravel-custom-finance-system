<?php

namespace App\Http\Controllers\Api\Info;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Api\Abstracts\SelectOptionsResource;
use App\Http\Resources\Api\Info\Contractor\ContractorByProjectContractorResource;
use App\Http\Resources\Api\Info\Contractor\ContractorByProjectResource;
use App\Models\Info\Contractor\Contractor;
use App\Models\Info\Project\Project;
use Illuminate\Http\Request;

class ContractorController extends ApiController
{
    public function fetchData(Request $request, $project_id = null)
    {
        /** @var Project $project */
        $project = null;
        if( $project_id ?? ($project_id = $request->project_id) ) {
            /** @var Project $project */
            $project = Project::onlyActive()
                              ->with([ 'contractors' => fn($q) => $q->onlyActive()->latest('id') ])
                              ->findOrFail($project_id);
        }

        $data = $project
            ? toCollect([ $project ])
            :
            Project::onlyActive()
                   ->with([ 'contractors' => fn($q) => $q->onlyActive()->latest('id') ])
                   ->latest('id')
                   ->get('id');

        return $this->success(ContractorByProjectResource::collection($data));
    }
}
