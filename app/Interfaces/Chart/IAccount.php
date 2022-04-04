<?php

namespace App\Interfaces\Chart;

use App\Interfaces\IHasStatusColumn;

interface IAccount extends IHasStatusColumn
{
    const
        ACTIVE = 1,
        INACTIVE = 2;

    const STATUSES = [
        'active' => self::ACTIVE,
        'inactive' => self::INACTIVE,
    ];
}
