<?php

namespace App\Policies\Sheet;

use App\Policies\Abstracts\BasePolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class CreditPolicy extends BasePolicy
{
    public static string $permission_name = 'credit';

}
