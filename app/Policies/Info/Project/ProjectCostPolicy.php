<?php

namespace App\Policies\Info\Project;

use App\Policies\Abstracts\BasePolicy;

class ProjectCostPolicy extends BasePolicy
{
    public static string $permission_name = 'project_cost';
    public static $displayInNavigation = false;

}
