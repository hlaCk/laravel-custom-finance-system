<?php

namespace App\Interfaces\Info;

use App\Interfaces\IHasStatusColumn;

interface IClient extends IHasStatusColumn
{
    const
        ACTIVE = 1,
        INACTIVE = 2;

    const STATUSES = [
        'active' => self::ACTIVE,
        'inactive' => self::INACTIVE,
    ];
}
