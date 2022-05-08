<?php

namespace App\Models\Abstracts;

use App\Traits\TModelTranslation;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model as BaseModel;
use Spatie\MediaLibrary\HasMedia;

class Model extends BaseModel implements HasMedia
{
    use BaseModelTrait;
}
