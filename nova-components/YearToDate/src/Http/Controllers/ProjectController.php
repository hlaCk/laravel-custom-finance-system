<?php

namespace Sheets\YearToDate\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\Abstracts\SelectOptionsResource;
use App\Models\Info\Project\Project;
use Illuminate\Http\Request;
use Sheets\YearToDate\Http\Resources\ProjectResource;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        return SelectOptionsResource::collection(Project::all(), 'name', 'id');
    }

    public function show(Request $request, Project $project)
    {
        return ProjectResource::make($project)->except(['id']);
    }
}
