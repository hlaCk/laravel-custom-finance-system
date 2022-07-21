<?php

namespace App\Models\Info\Contractor;

use App\Interfaces\IBooleanStatus;
use App\Models\Abstracts\Model;
use App\Models\Info\Project\Project;
use App\Models\Sheet\Expense;
use App\Traits\THasBooleanStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static Builder|static expensesByDate($from_date = null, $entry_category_id = null)
 * @see Contractor::scopeExpensesByDate()
 *
 */
class Contractor extends Model implements IBooleanStatus
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
        'contractor_speciality_id',
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
        'name'                     => 'string',
        'contractor_speciality_id' => 'integer',
        'status'                   => 'integer',
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

    public function contractor_speciality()
    {
        return $this->belongsTo(ContractorSpeciality::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function projects()
    {
        return $this->belongsToMany(Project::class)
                    ->using(ContractorProject::class)
                    ->withPivot([
                                    'date',
                                    'remarks',
                                    'unit',
                                    'quantity',
                                    'price',
                                    //                                    'total',
                                    'deleted_at',
                                ])
                    ->withTimestamps();
    }

    public function getContractorSpecialityNameAttribute()
    {
        return ($contractor_speciality = $this->contractor_speciality) ? $contractor_speciality->name : "";
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param bool|\Closure|string                  $latest
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo|Builder
     */
    public function scopeLatestActive(Builder $builder, $latest = true)
    {
        $latest = is_bool($latest = value($latest)) ? iif($latest === true, 'desc', 'asc') : $latest;
        return $builder
            ->orderBy($builder->getModel()->getQualifiedKeyName(), $latest)
            ->onlyActive();
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

        if( $projects ) {
            return $builder->whereHas(
                'projects',
                fn(Builder $q) => $q->whereIn(Project::make()->getQualifiedKeyName(), $projects)
            );
        }

        return $builder;
    }
}
