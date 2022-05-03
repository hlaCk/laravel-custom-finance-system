<?php

namespace App\Policies\Info\Contractor;

use App\Policies\Abstracts\BasePolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class ContractorPolicy extends BasePolicy
{
    public static string $permission_name = 'contractor';

}
