<?php

namespace App\Models\Abstracts;

use Spatie\MediaLibrary\MediaCollections\Models\Media as SpatieMedia;
use Illuminate\Database\Eloquent\SoftDeletes;

class Media extends SpatieMedia
{
    use SoftDeletes;
}
