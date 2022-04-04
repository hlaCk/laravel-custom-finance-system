<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

/**
 * @mixin \App\Models\Abstracts\Model
 */
trait THasOrderColumn
{
    /** @var string */
    public static string $ORDER_COLUMN_NAME = 'order';
    /** @var string */
    public static string $ORDER_COLUMN_TYPE = 'integer';

    /**
     * @return string
     */
    public static function getOrderColumnName(): string
    {
        return self::$ORDER_COLUMN_NAME;
    }

    /**
     * @return string
     */
    public static function getOrderColumnType(): string
    {
        return self::$ORDER_COLUMN_TYPE;
    }

    public function initializeTHasOrderColumn()
    {
        $column_name = static::getOrderColumnName();
        $column_type = static::getOrderColumnType();
        // check fillable
        if( $column_name && !$this->isFillable($column_name) ) {
            $this->mergeFillable([ $column_name ]);
        }

        // check casts
        if( $column_type && !$this->isCastable($column_name, $column_type) ) {
            $this->mergeCasts([
                                  $column_name => $column_type,
                              ]);
        }
    }

    /**
     * Determine if the given attribute exists in cast array.
     *
     * @param string      $key
     * @param string|null $type
     *
     * @return bool
     */
    public function isCastable(string $key, ?string $type = null): bool
    {
        $casts = $this->getCasts();
        if( isset($casts[ $key ]) ) {
            if( !is_null($type) && $type && snake_case($casts[ $key ]) !== snake_case($type) ) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int|int[]                             $order
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByOrder(Builder $builder, $order): Builder
    {
        return $builder->whereIn(static::getOrderColumnName(), (array) $order);
    }

    public function updateOrder(int $order): bool
    {
        return $this->update([
                                 static::getOrderColumnName() => $order,
                             ]);
    }

    public function exchangeOrder(int $newOrder): bool
    {
        if( $modelWithNewOrder = static::byOrder($newOrder)->first() ) {
            if( !$modelWithNewOrder->updateOrder($this->order) ) {
                return false;
            }
        }

        return $this->updateOrder($newOrder);
    }
}
