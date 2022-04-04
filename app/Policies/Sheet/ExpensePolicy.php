<?php

namespace App\Policies\Sheet;

use App\Policies\Abstracts\BasePolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class ExpensePolicy extends BasePolicy
{
    public static string $permission_name = 'expense';

}
