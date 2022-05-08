<?php

namespace App\Models\Abstracts;

use App\Traits\TModelTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot as BaseModel;
use Spatie\MediaLibrary\HasMedia;

class Pivot extends BaseModel implements HasMedia
{
    use BaseModelTrait;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];
}
