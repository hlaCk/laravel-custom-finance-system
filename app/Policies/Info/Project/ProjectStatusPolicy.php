<?php

namespace App\Policies\Info\Project;

use App\Policies\Abstracts\BasePolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectStatusPolicy extends BasePolicy
{
    public static string $permission_name = 'project_status';

}
