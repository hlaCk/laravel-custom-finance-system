<?php

namespace App\Traits;

/**
 * @mixin \App\Models\Abstracts\Model
 */
trait THasNextNewId
{

    /**
     * @return int
     */
    public static function getNextNewId()
    {
        return static::count() + 1;
    }
}
