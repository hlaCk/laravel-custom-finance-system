<?php

namespace App\Traits;

use App\Interfaces\IBooleanStatus;

/**
 * @mixin \App\Models\Abstracts\Model
 */
trait THasBooleanStatus
{
    public static function getAllStatuses(): ?array
    {
        return (array)static::trans("statuses");
    }

    public function getStatusLabelAttribute()
    {
        return static::getStatusLabel($this->status);
    }

    public static function getStatusLabel(int $status): string
    {
        return trim(static::trans("statuses.{$status}"));
    }
}
