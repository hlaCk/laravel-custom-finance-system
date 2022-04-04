<?php

namespace App\Interfaces;

interface IBooleanStatus extends IHasStatusColumn
{
    const
        ACTIVE = 1,
        INACTIVE = 2;

    const STATUSES = [
        'active' => self::ACTIVE,
        'inactive' => self::INACTIVE,
    ];
}
