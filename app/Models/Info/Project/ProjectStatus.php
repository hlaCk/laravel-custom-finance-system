<?php

namespace App\Models\Info\Project;

use App\Models\Abstracts\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static Builder|static byName($name)
 * @see ProjectStatus::scopeByName()
 *
 */
class ProjectStatus extends Model
{
    use HasFactory;
    use SoftDeletes;
    use \App\Traits\HasTranslations;

    public $translatable = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
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
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function projects()
    {
        return $this->hasMany(Project::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param array|string|null                     $name
     *
     * @return Builder
     */
    public function scopeByName(Builder $builder, $name)
    {
        return $builder->whereIn('name', (array) $name);
    }
}
