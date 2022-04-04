<?php

namespace App\Policies\Chart;

use App\Policies\Abstracts\BasePolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class AccountPolicy extends BasePolicy
{
    public static string $permission_name = 'account';

}
