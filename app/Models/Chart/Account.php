<?php

namespace App\Models\Chart;

use App\Interfaces\Chart\IAccount;
use App\Models\Abstracts\Model;
use App\Traits\THasBooleanStatus;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Account extends Model implements IAccount
{
    use HasFactory;
    use SoftDeletes;
    use THasBooleanStatus;
    use \App\Traits\HasTranslations;

    const DEFAULT_TYPE = 'budget';

    public $translatable = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'type',
        'status',
        'account_id',
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
        'name'       => 'string',
        'type'       => 'string',
        'status'     => 'integer',
        'account_id' => 'integer',
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

    public function account()
    {
        return $this->belongsTo(Account::class);
    }

    public function sub_accounts()
    {
        return $this->hasMany(Account::class, 'account_id', 'id');
    }

    public function scopeOnlyParents(Builder $query)
    {
        return $query->where(fn($q) => $q->whereNull('account_id')->orWhere('account_id', 0));
    }

    public function scopeOnlySubs(Builder $query)
    {
        return $query->where(fn(Builder $q) => $q->whereNotNull('account_id')->orWhereNotIn('account_id', [ 0 ]));
    }
}
