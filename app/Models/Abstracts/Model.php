<?php

namespace App\Models\Abstracts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Spatie\MediaLibrary\HasMedia;

class Model extends BaseModel implements HasMedia
{
    use BaseModelTrait;

    /**
     * Get an array with the values of a given column.
     *
     * @param string|\Illuminate\Database\Query\Expression $column
     * @param string|null                                  $key
     *
     * @return array
     */
    public function scopeToIndexedArrayPluck(Builder $builder, $column = 'name')
    {
        return $builder->toArrayPluck($column, null);
    }

    /**
     * Get an array with the values of a given column.
     *
     * @param string|\Illuminate\Database\Query\Expression $column
     * @param string|null                                  $key
     *
     * @return array
     */
    public function scopeToArrayPluck(Builder $builder, $column = 'name', $key = true)
    {
        $key = $key === true ? $builder->getModel()->getQualifiedKeyName() : $key;
        return $builder
            ->pluck($column, $key)
            ->toArray();
    }
}
