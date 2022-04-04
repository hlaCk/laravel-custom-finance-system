<?php

namespace App\Models\Settings;

use App\Interfaces\IBooleanStatus;
use App\Models\Abstracts\Model;
use App\Traits\THasBooleanStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;

class Setting extends Model implements IBooleanStatus
{
    use HasFactory;
    use THasBooleanStatus;
    use HasTranslations;

    public $translatable = [
        'value',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'key',
        'value',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
//        'created_at',
//        'updated_at',
//        'deleted_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'key'    => 'string',
        'value'  => 'string',
        'status' => 'integer',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public static function getDefaultStatus(): int
    {
        return static::ACTIVE;
    }
}
