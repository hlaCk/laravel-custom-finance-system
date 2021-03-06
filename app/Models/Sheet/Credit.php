<?php

namespace App\Models\Sheet;

use App\Interfaces\Sheet\ICredit;
use App\Models\Abstracts\Model;
use App\Models\Info\CreditCategory;
use App\Models\Info\Project\Project;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static Builder|static byDate($from_date = null, $credit_category_id = null, $project_id = null)
 * @see Credit::scopeByDate()
 *
 */
class Credit extends Model
{
    use HasFactory;
    use SoftDeletes;
    use \App\Traits\HasTranslations;
    use \App\Traits\TBelongsToProject;
    use \App\Traits\Sheet\TFormatDateAttribute;

    public $translatable = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'date',
        'amount',
        'vat_included',
        'remarks',
        'project_id',
        'credit_category_id',
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
        'date'               => 'date',
        'amount'             => 'double',
        'vat_included'       => 'boolean',
        'remarks'            => 'string',
        'project_id'         => 'integer',
        'credit_category_id' => 'integer',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'date',
    ];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function credit_category()
    {
        return $this->belongsTo(CreditCategory::class);
    }

    public function getCreditCategoryNameAttribute()
    {
        return optional($this->credit_category)->name;
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \DateTime|string|null                 $from_date
     * @param array|int|string|null                 $credit_category_id
     * @param array|int|string|null                 $project_id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Builder
     */
    public function scopeByDate(Builder $builder, $from_date = null, $credit_category_id = null, $project_id = null)
    {
        return $builder->when(
            !is_null($project_id),
            fn($q) => $q->whereIn('project_id', (array) $project_id)
        )
                       ->when(
                           !is_null($credit_category_id),
                           fn($q) => $q->whereIn('credit_category_id', (array) $credit_category_id)
                       )
                       ->when(
                           !is_null($from_date),
                           fn($q) => $q->whereDate('date', '>=', $from_date)
                       );
//            ;
    }

}
