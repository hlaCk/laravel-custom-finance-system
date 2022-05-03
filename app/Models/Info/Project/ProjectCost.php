<?php

namespace App\Models\Info\Project;

use App\Models\Abstracts\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @method static Builder|static ByProject($project)
 * @see \App\Models\Info\Project\ProjectCost::scopeByProject()
 *
 */
class ProjectCost extends Model
{
    use HasFactory;
    use \App\Traits\HasTranslations;

    public $translatable = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'cost',
        'project_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
//        'created_at',
//        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'name'              => 'string',
        'cost'              => 'double',
        'project_id' => 'integer',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder                                         $builder
     * @param int|int[]|\App\Models\Info\Project\Project|\App\Models\Info\Project\Project[] $project
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Builder
     */
    public function scopeByProject(Builder $builder, $project)
    {
        $projects = toCollectWithModel($project)
            ->map(fn($p) => $p instanceof \Illuminate\Database\Eloquent\Model ? $p->id : $p)
            ->filter()
            ->toArray();

        return $builder->whereIn('project_id', $projects);
    }

    public function getProjectNameAttribute()
    {
        return ($project = $this->project) ? $project->name : "";
    }
}
