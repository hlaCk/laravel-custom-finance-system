<?php

namespace App\Policies\Info;

use App\Policies\Abstracts\BasePolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class CreditCategoryPolicy extends BasePolicy
{
    public static string $permission_name = 'credit_category';

}
