<?php

namespace App\Traits;

use App\Interfaces\IBooleanStatus;
use App\Models\Info\Project\Project;
use Illuminate\Database\Eloquent\Builder;

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

    public function scopeByStatus(Builder $query, $status = IBooleanStatus::ACTIVE)
    {
        return $query->whereIn('status', (array)$status);
    }

    public function scopeOnlyActive(Builder $query)
    {
        return $query->byStatus(IBooleanStatus::ACTIVE);
    }

    public function scopeOnlyInActive(Builder $query)
    {
        return $query->byStatus(IBooleanStatus::INACTIVE);
    }
}
