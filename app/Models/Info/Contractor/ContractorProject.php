<?php

namespace App\Models\Info\Contractor;

use App\Models\Abstracts\Pivot;
use App\Models\Info\Project\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class ContractorProject extends Pivot
{
    use HasFactory;
    use SoftDeletes;
    use \App\Traits\HasTranslations;

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    public $translatable = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'date',
        'remarks',
        'unit',
        'quantity',
        'price',
        //        'total',
        'contractor_id',
        'project_id',
    ];

    protected $appends = [
        'total',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'date'          => 'datetime',
        'remarks'       => 'string',
        'unit'          => 'string',
        'quantity'      => 'double',
        'price'         => 'double',
        'total'         => 'double',
        'contractor_id' => 'integer',
        'project_id'    => 'integer',
    ];

    protected $dates = [
        'date',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function getTotalAttribute()
    {
        return (double) ((double) $this->quantity * (double) $this->price);
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
            ->map(fn($p) => isModel($p) ? $p->id : $p)
            ->filter()
            ->toArray();

        return $builder->whereIn('project_id', $projects);
    }
}
