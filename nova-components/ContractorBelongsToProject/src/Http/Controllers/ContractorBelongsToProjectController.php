<?php

namespace Info\Components\Http\Controllers;

use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Api\Abstracts\SelectOptionsResource;
use App\Models\Info\Contractor\Contractor;
use App\Models\Info\Project\Project;
use Illuminate\Http\Request;

class ContractorBelongsToProjectController extends ApiController
{
    public function fetchData(Request $request, $resource = null, $resourceId = null)
    {
        /** @var Project $project */
        $project = null;
        if( $project_id = $request->project_id ) {
            /** @var Project $project */
            $project = Project::onlyActive()
                              ->with([ 'contractors' => fn($q) => $q->onlyActive()->latest('id') ])
                              ->findOrFail($project_id);
        }

        $data = $project ? $project->contractors : Contractor::onlyActive()->latest('id');

        return $this->success([
                                  'data'       => (array) SelectOptionsResource::collection($data)->toArray($request),
                                  'project_id' => (int) $project_id,
                              ]);
    }
}
