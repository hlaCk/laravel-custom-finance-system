<?php

namespace App\Models\Info\Project;

use App\Interfaces\IBooleanStatus;
use App\Models\Abstracts\Model;
use App\Models\Sheet\Credit;
use App\Models\Sheet\Expense;
use App\Traits\THasBooleanStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Nova\Fields\Currency;

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
        'cost',
        'project_status_id',
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
        'cost'              => 'double',
        'status'            => 'integer',
        'project_status_id' => 'integer',
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
     * @param \DateTime|string|null                 $from_date
     * @param array|int|string|null                 $credit_category_id
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany|Builder
     */
    public function scopeCreditsByDate(Builder $builder, $from_date = null, $credit_category_id = null)
    {
        return $this->credits()
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
                            fn($model) => $model->date->format(value($groupBy))
                        )
                    )
                    ->when(
                        $group_by_entry_categories && !is_null($groupBy),
                        fn($q) => $q->map(fn($model) => $model->map->sum('amount')),
                        fn($q) => $q->map->sum('amount')
                    );
    }

    /**
     * @param int|int[]|null        $credit_category_id
     * @param \DateTime|string|null $from_date default: `getDefaultFromDate()`
     *
     * @return \Illuminate\Support\Collection
     */
    public function credits_ytd($credit_category_id = null, $from_date = null)
    {
        $from_date ??= getDefaultFromDate();
        $credit_category_id =
            $credit_category_id === '*' || $credit_category_id === [ '*' ] ? null : $credit_category_id;
        return $this->creditsByDate($from_date, $credit_category_id)
                    ->with('credit_category')
                    ->get([
                              'amount',
                              'date',
                              'credit_category_id',
                          ])
                    ->groupBy(fn($model) => optional($model->credit_category)->name)
            ->map->sum('amount');
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
                            fn($model) => $model->date->format(value($groupBy))
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

    public function getProjectStatusNameAttribute()
    {
        return ($project_status = $this->project_status) ? $project_status->name : "";
    }

    public function getCostLabelAttribute()
    {
        return Currency::make($attr = 'cost')
                       ->formatMoney($this->$attr, null, currentLocale());
    }

    public function getCreditTotalAttribute()
    {
        return (double) $this->credits_ytd_by_month(request()->credit_category_id, request()->from_date)
            ->sum('amount')
        ;
    }

    public function getCreditTotalLabelAttribute()
    {
        return Currency::make($attr = 'credit_total')
                       ->formatMoney($this->$attr, null, currentLocale());
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

    public function getExpensesTotalLabelAttribute()
    {
        return Currency::make($attr = 'expenses_total')
                       ->formatMoney($this->$attr, null, currentLocale());
    }

    public function getBalanceAttribute()
    {
        return (double) $this->credit_total - (double) $this->expenses_total;
    }

    public function getBalanceLabelAttribute()
    {
        return Currency::make($attr = 'balance')
                       ->formatMoney($this->$attr, null, currentLocale());
    }

    public function getRemainingAttribute()
    {
        return (double) $this->cost - (double) $this->credit_total;
    }

    public function getRemainingLabelAttribute()
    {
        return Currency::make($attr = 'remaining')
                       ->formatMoney($this->$attr, null, currentLocale());
    }
}
