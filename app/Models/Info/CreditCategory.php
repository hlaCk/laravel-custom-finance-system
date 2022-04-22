<?php

namespace App\Models\Info;

use App\Interfaces\IBooleanStatus;
use App\Models\Abstracts\Model;
use App\Models\Sheet\Credit;
use App\Traits\THasBooleanStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class CreditCategory extends Model implements IBooleanStatus
{
    use HasFactory;
    use SoftDeletes;
    use THasBooleanStatus;
    use \App\Traits\HasTranslations;

    public $translatable = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
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
        'name'   => 'string',
        'status' => 'integer',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public static function getDefaultStatus(): int
    {
        return static::ACTIVE;
    }

    public function credits()
    {
        return $this->hasMany(Credit::class);
    }
}
