<?php

namespace App\Policies\Info\Project;

use App\Policies\Abstracts\BasePolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProjectPolicy extends BasePolicy
{
    public static string $permission_name = 'project';

}
