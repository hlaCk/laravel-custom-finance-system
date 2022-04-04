<?php

namespace App\Helpers\Classes;

use Illuminate\Contracts\Support\Arrayable;

class ArrayMap extends \Illuminate\Support\Collection
{
    /** @var array */
    protected array $keysMap = [];

    /**
     * Create a new collection.
     *
     * @param Arrayable|array|mixed $items
     * @param \Closure|array|null   $keysMap
     */
    public function __construct($items = [], $keysMap = null)
    {
        parent::__construct($items);
        $this->keysMap($keysMap);
        $this->runKeyMap();
    }

    /**
     * Get/Set keysMap
     *
     * @param \Closure|array|null $keys
     *
     * @return self|array
     */
    public function keysMap($keys = null)
    {
        if( func_num_args() === 0 ) {
            return $this->keysMap;
        }

        $this->keysMap = (array) value($keys, $this, $this->keysMap);

        return $this;
    }

    /**
     * @param \Closure|string $key
     * @param mixed|null      $default
     *
     * @return string|mixed|null
     */
    public function getMappedKey($key, $default = null)
    {
        return ($key = value($key)) ? $this->keysMap[ $key ] ?? $default : $default;
    }

    protected function runKeyMap()
    {
        $newItems = [];
        // if( isAssocArray($this->items) ) {
        foreach( $this->items as $key => $item ) {
            if( $_key = $this->getMappedKey($key) ) {
                $newItems[ $_key ] = $this->items[ $key ];
                continue;
            }

            $newItems[ $key ] = $item;
        }
        // }
        $this->items = $newItems;

        return $this;
    }

    /**
     * Create a new collection.
     *
     * @param Arrayable|array|mixed $items
     * @param \Closure|array|null   $keysMap
     *
     * @return self
     */
    public function setItems($items = [], $keysMap = null): self
    {
        return static::make(...func_get_args());
    }

    /**
     * Create a new collection.
     *
     * @param Arrayable|array|mixed $items
     * @param \Closure|array|null   $keysMap
     *
     * @return self
     */
    public static function make($items = [], $keysMap = null): self
    {
        return new static($items, $keysMap);
    }

    public function __get($key)
    {
        if( $this->has($key) ) {
            return $this->get($key);
        }

        return parent::__get(...func_get_args());
    }
}
