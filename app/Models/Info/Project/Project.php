<?php

namespace App\Models\Info\Project;

use App\Interfaces\IBooleanStatus;
use App\Models\Abstracts\Model;
use App\Models\Info\Client;
use App\Models\Info\Contractor\Contractor;
use App\Models\Info\Contractor\ContractorProject;
use App\Models\Sheet\Credit;
use App\Models\Sheet\Expense;
use App\Traits\THasBooleanStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @method static Builder|static expensesByDate($from_date = null, $entry_category_id = null)
 * @see Project::scopeExpensesByDate()
 *
 */
class Project extends Model implements IBooleanStatus
{
    use HasFactory;
    use SoftDeletes;
    use THasBooleanStatus;
    use \App\Traits\HasTranslations;

    const DEFAULT_PROJECT_STATUS_NAME = 'New';

    public $translatable = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'base_cost',
        'project_status_id',
        'client_id',
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
        'name'              => 'string',
        'base_cost'         => 'double',
        'status'            => 'integer',
        'project_status_id' => 'integer',
        'client_id'         => 'integer',
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

    public function project_status()
    {
        return $this->belongsTo(ProjectStatus::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function contractors()
    {
        return $this->belongsToMany(Contractor::class)
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

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \DateTime|string|null                 $from_date
     * @param array|int|string|null                 $entry_category_id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Builder
     */
    public function scopeExpensesByDate(Builder $builder, $from_date = null, $entry_category_id = null)
    {
        return $this->expenses()
                    ->when(
                        !is_null($entry_category_id),
                        fn($q) => $q->whereIn('entry_category_id', (array) $entry_category_id)
                    )
                    ->when(
                        !is_null($from_date),
                        fn($q) => $q->whereDate('date', '>=', $from_date)
                    );
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }


    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param int|int[]|null                        $contractor
     *
     * @return Builder
     */
    public function scopeByContractor(Builder $builder, $contractor = null)
    {
        return $builder->whereHas('contractors', fn($q) => $q->whereIn('contractor_id', (array) $contractor));
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $builder
     * @param \DateTime|string|null                 $from_date
     * @param array|int|string|null                 $credit_category_id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Builder
     */
    public function scopeCreditsByDate(
        Builder $builder,
                $from_date = null,
                $credit_category_id = null,
                $project_id = null
    ) {
        return $this->credits()
                    ->when(
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
    }

    public function credits()
    {
        return $this->hasMany(Credit::class);
    }

    /**
     * @param int|int[]|null        $entry_category_id
     * @param \DateTime|string|null $from_date default: `getDefaultFromDate()`
     *
     * @return \Illuminate\Support\Collection
     */
    public function expenses_ytd($entry_category_id = null, $from_date = null)
    {
        $from_date ??= getDefaultFromDate();
        $entry_category_id = $entry_category_id === '*' || $entry_category_id === [ '*' ] ? null : $entry_category_id;
        return $this->expensesByDate($from_date, $entry_category_id)
                    ->with('entry_category')
                    ->get([
                              'amount',
                              'date',
                              'entry_category_id',
                          ])
                    ->groupBy(fn($model) => optional($model->entry_category)->name)
            ->map->sum('amount');
    }

    /**
     * @param int|int[]|null        $credit_category_id
     * @param \DateTime|string|null $from_date default: `getDefaultFromDate()`
     * @param \Closure|null         $group_by  null = group by credit_category name
     *
     * @return \Illuminate\Support\Collection
     */
    public function credits_ytd($credit_category_id = null, $from_date = null, ?\Closure $group_by = null)
    {
        $group_by ??= fn($model) => optional($model->credit_category)->name;
        $from_date ??= getDefaultFromDate();
        $credit_category_id =
            $credit_category_id === '*' || $credit_category_id === [ '*' ] ? null : $credit_category_id;
        return $this->creditsByDate($from_date, $credit_category_id)
                    ->with([ 'credit_category', 'project' ])
                    ->get([
                              'amount',
                              'date',
                              'credit_category_id',
                              'project_id',
                          ])
                    ->groupBy($group_by)
            ->map->sum('amount');
    }

    public function getProjectStatusNameAttribute()
    {
        return ($project_status = $this->project_status) ? $project_status->name : "";
    }

    public function getCreditTotalAttribute()
    {
        return (double) $this->credits_ytd_by_month(request()->credit_category_id, request()->from_date)
                             ->sum('amount');
    }

    /**
     * @param int|int[]|null        $credit_category_id
     * @param \DateTime|string|null $from_date default: `getDefaultFromDate()`
     * @param string|\Closure|null  $groupBy
     *
     * @return \Illuminate\Support\HigherOrderCollectionProxy
     */
    public function credits_ytd_by_month($credit_category_id = null, $from_date = null, $groupBy = 'M/Y')
    {
        $from_date ??= getDefaultFromDate();
        $group_by_credit_categories =
            $credit_category_id === '*' || $credit_category_id === [ '*' ] || !is_null($credit_category_id);
        $credit_category_id =
            $credit_category_id === '*' || $credit_category_id === [ '*' ] ? null : $credit_category_id;
        return $this->creditsByDate($from_date, $credit_category_id)
                    ->with('credit_category')
                    ->get([
                              'amount',
                              'date',
                              'credit_category_id',
                          ])
                    ->when(
                        $group_by_credit_categories,
                        fn($q) => $q->groupBy(fn($model) => $model->credit_category_name)
                    )
                    ->when(
                        !is_null($groupBy),
                        fn($q) => ($group_by_credit_categories ? $q->map : $q)->groupBy(
                            fn($model) => $model->formatDate(value($groupBy))
                        )
                    )
                    ->when(
                        $group_by_credit_categories && !is_null($groupBy),
                        fn($q) => $q->map(fn($model) => $model->map->sum('amount')),
                        fn($q) => $q->map(fn($qq) => [
                            'count'  => $qq->count(),
                            'amount' => $qq->sum('amount'),
                        ])
                    );
    }

    public function getCreditTotalLabelAttribute()
    {
        return formatValueAsCurrency($this->credit_total);
    }

    public function getCreditCountAttribute()
    {
        return (double) $this->credits_ytd_by_month(request()->credit_category_id, request()->from_date)
                             ->sum('count');
    }

    public function getExpensesTotalAttribute()
    {
        return (double) $this->expenses_ytd_by_month(null, request()->from_date)
                             ->sum();
    }

    /**
     * @param int|int[]|null        $entry_category_id
     * @param \DateTime|string|null $from_date default: `getDefaultFromDate()`
     * @param string|\Closure|null  $groupBy
     *
     * @return \Illuminate\Support\HigherOrderCollectionProxy
     */
    public function expenses_ytd_by_month($entry_category_id = null, $from_date = null, $groupBy = 'M/Y')
    {
        $from_date ??= getDefaultFromDate();
        $group_by_entry_categories =
            $entry_category_id === '*' || $entry_category_id === [ '*' ] || !is_null($entry_category_id);
        $entry_category_id = $entry_category_id === '*' || $entry_category_id === [ '*' ] ? null : $entry_category_id;

        return $this->expensesByDate($from_date, $entry_category_id)
                    ->with('entry_category')
                    ->get([
                              'amount',
                              'date',
                              'entry_category_id',
                          ])
                    ->when(
                        $group_by_entry_categories,
                        fn($q) => $q->groupBy(fn($model) => $model->entry_category_name)
                    )
                    ->when(
                        !is_null($groupBy),
                        fn($q) => ($group_by_entry_categories ? $q->map : $q)->groupBy(
                            fn($model) => $model->formatDate(value($groupBy))
                        )
                    )
                    ->when(
                        $group_by_entry_categories && !is_null($groupBy),
                        fn($q) => $q->map(fn($model) => $model->map->sum('amount')),
                        fn($q) => $q->map->sum('amount')
                    );
    }

    public function getExpensesTotalLabelAttribute()
    {
        return formatValueAsCurrency($this->expenses_total);
    }

    public function getBalanceAttribute()
    {
        return (double) $this->credit_total - (double) $this->expenses_total;
    }

    public function getBalanceLabelAttribute()
    {
        return formatValueAsCurrency($this->balance);
    }

    public function getRemainingAttribute()
    {
        return (double) $this->cost - (double) $this->credit_total;
    }

    public function getRemainingLabelAttribute()
    {
        return formatValueAsCurrency($this->remaining);
    }

    public function getCostsAttribute()
    {
        return $this->project_costs()->sum('cost');
    }

    // region: costs

    public function project_costs()
    {
        return $this->hasMany(ProjectCost::class);
    }

    public function getCostsLabelAttribute()
    {
        return formatValueAsCurrency($this->costs);
    }

    public function getCostAttribute()
    {
        return $this->costs + $this->base_cost;
    }

    public function getCostLabelAttribute()
    {
        return formatValueAsCurrency($this->cost);
    }

    public function getBaseCostLabelAttribute()
    {
        return formatValueAsCurrency($this->base_cost);
    }

    // endregion: costs
}
