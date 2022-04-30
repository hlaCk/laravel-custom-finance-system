<?php

namespace App\Traits;

/**
 * @property \App\Models\Info\Project\Project $project
 * @property string|null $project_name
 */
trait TBelongsToProject
{
    public function getProjectNameAttribute()
    {
        return optional($this->project)->name;
    }
}
