<?php

namespace App\Policies\Info\Project;

use App\Policies\Abstracts\BasePolicy;

class ProjectStatusPolicy extends BasePolicy
{
    public static string $permission_name = 'project_status';

}
