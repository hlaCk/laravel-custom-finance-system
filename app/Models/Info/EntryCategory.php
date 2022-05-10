<?php

namespace App\Models\Info;

use App\Interfaces\IBooleanStatus;
use App\Models\Abstracts\Model;
use App\Models\Sheet\Expense;
use App\Traits\THasBooleanStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read bool $isHasContractor
 * @see EntryCategory::getIsHasContractorAttribute()
 */
class EntryCategory extends Model implements IBooleanStatus
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
        'has_contractor',
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
        'name'           => 'string',
        'has_contractor' => 'boolean',
        'status'         => 'integer',
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

    /**
     * @param Model|int $entry_category_id
     *
     * @return bool
     */
    public static function isHasContractor($entry_category_id)
    {
        $entry_category =
            $entry_category_id instanceof Model ? $entry_category_id : EntryCategory::find($entry_category_id);

        return $entry_category->has_contractor;
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function getIsHasContractorAttribute()
    {
        return $this->has_contractor;
    }

    public function scopeOnlyHasContractor(Builder $builder)
    {
        return $builder->where('has_contractor', true);
    }

    public function scopeOnlyDoesntHaveContractor(Builder $builder)
    {
        return $builder->where('has_contractor', false);
    }
}
