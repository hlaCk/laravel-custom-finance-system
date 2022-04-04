<?php

namespace App\Policies\Settings;

use App\Policies\Abstracts\BasePolicy;
use Illuminate\Auth\Access\HandlesAuthorization;

class SettingPolicy extends BasePolicy
{
    public static string $permission_name = 'setting';

}
