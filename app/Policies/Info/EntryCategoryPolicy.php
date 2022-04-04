<?php

namespace App\Policies\Info;

use App\Policies\Abstracts\BasePolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class EntryCategoryPolicy extends BasePolicy
{
    public static string $permission_name = 'entry_category';

}
