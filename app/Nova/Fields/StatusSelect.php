<?php

namespace App\Nova\Fields;

use App\Nova\Abstracts\Resource;
use Laravel\Nova\Fields\Select;

/**
 *
 */
class StatusSelect extends Select
{
    /**
     * The displayable name of the field.
     *
     * @var string
     */
    public $name = 'status';

    /**
     * The attribute / column name of the field.
     *
     * @var string
     */
    public $attribute = 'status';

    /**
     * Create a new field.
     *
     * @param string|null          $name
     * @param string|callable|null $attribute
     * @param callable|null        $resolveCallback
     *
     * @return void
     */
    public function __construct($name = null, $attribute = null, callable $resolveCallback = null)
    {
        parent::__construct($name ?? $this->name, $attribute ?? $this->attribute, $resolveCallback);
    }

    /**
     * Make new field for the given resource.
     *
     * @param \App\Nova\Abstracts\Resource|null $resource
     *
     * @return self
     */
    public static function forResource(?Resource $resource = null): self
    {
        $model = currentNovaResourceModelClass();

        return static::make()
                     ->transName()
                     ->setResource($resource ?? currentNovaResourceClass())
                     ->options($model::getAllStatuses())
                     ->default($model::getDefaultStatus())
                     ->sortable()
                     ->displayUsing(fn($a) => $model::getStatusLabel($a));
    }

    /**
     * @param Resource|string $resource
     *
     * @return $this
     */
    public function setResource($resource): self
    {
        $this->resource = $resource;

        return $this;
    }

    /**
     * returns translated name.
     *
     * @return $this
     */
    public function transName(): self
    {
        $this->name = currentNovaResourceModelClass(fn($model) => $model::trans($this->name));

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
