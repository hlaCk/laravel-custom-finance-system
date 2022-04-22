<?php

namespace App\Interfaces\Sheet;

use App\Interfaces\IHasStatusColumn;

interface ICredit extends IHasStatusColumn
{
    const
        ACTIVE = 1,
        INACTIVE = 2;

    const STATUSES = [
        'active' => self::ACTIVE,
        'inactive' => self::INACTIVE,
    ];
}
