<?php

namespace App\Models\Info;

use App\Interfaces\Info\IClient;
use App\Models\Abstracts\Model;
use App\Models\Info\Project\Project;
use App\Traits\THasBooleanStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model implements IClient
{
    use HasFactory;
    use SoftDeletes;
    use THasBooleanStatus;
    use \App\Traits\HasTranslations;

    const DEFAULT_TYPE = 'cash';

    public $translatable = [ 'name' ];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name', 'type', 'status',
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
        'name' => 'string',
        'type' => 'string',
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

    public function projects()
    {
        return $this->hasMany(Project::class);
    }
}
