<?php

namespace App\Models\Info\Project;

use App\Interfaces\IBooleanStatus;
use App\Models\Abstracts\Model;
use App\Models\Sheet\Expense;
use App\Traits\THasBooleanStatus;
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
     * @param int|int[]|null  $entry_category_id
     * @param string|\Closure $groupBy
     *
     * @return \Illuminate\Support\HigherOrderCollectionProxy
     */
    public function expenses_ytd_by_month($entry_category_id = null, $groupBy = 'M/Y')
    {
        $entry_category_id = $entry_category_id === '*' || $entry_category_id === [ '*' ] ? null : $entry_category_id;
        return $this->expenses()
                    ->when(
                        !is_null($entry_category_id),
                        fn($q) => $q->whereIn('entry_category_id', (array) $entry_category_id)
                    )
                    ->whereDate('date', '>=', now()->firstOfYear())
                    ->with('entry_category')
                    ->get([
                              'amount',
                              'date',
                              'entry_category_id',
                          ])
                    ->groupBy(fn($model) => optional($model->entry_category)->name)
            ->map
            ->groupBy(fn($model) => $model->date->format(value($groupBy)))
            ->map(fn($byDate) => $byDate->map->sum('amount'));
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    /**
     * @param int|int[]|null $entry_category_id
     *
     * @return \Illuminate\Support\Collection
     */
    public function expenses_ytd($entry_category_id = null)
    {
        $entry_category_id = $entry_category_id === '*' || $entry_category_id === [ '*' ] ? null : $entry_category_id;
        return $this->expenses()
                    ->when(
                        !is_null($entry_category_id),
                        fn($q) => $q->whereIn('entry_category_id', (array) $entry_category_id)
                    )
                    ->whereDate('date', '>=', now()->firstOfYear())
                    ->with('entry_category')
                    ->get([
                              'amount',
                              'date',
                              'entry_category_id',
                          ])
                    ->groupBy(fn($model) => optional($model->entry_category)->name)
            ->map->sum('amount');
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
        return 0;
    }

    public function getCreditTotalLabelAttribute()
    {
        return Currency::make($attr = 'credit_total')
                       ->formatMoney($this->$attr, null, currentLocale());
    }

    public function getCreditCountAttribute()
    {
        return 0;
    }

    public function getExpensesTotalAttribute()
    {
        return 0;
    }

    public function getExpensesTotalLabelAttribute()
    {
        return Currency::make($attr = 'expenses_total')
                       ->formatMoney($this->$attr, null, currentLocale());
    }

    public function getBalanceAttribute()
    {
        return 0;
    }

    public function getBalanceLabelAttribute()
    {
        return Currency::make($attr = 'balance')
                       ->formatMoney($this->$attr, null, currentLocale());
    }

    public function getRemainingAttribute()
    {
        return 0;
    }

    public function getRemainingLabelAttribute()
    {
        return Currency::make($attr = 'remaining')
                       ->formatMoney($this->$attr, null, currentLocale());
    }
}
