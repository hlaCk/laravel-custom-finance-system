<?php

namespace App\Models\Info\Contractor;

use App\Interfaces\IBooleanStatus;
use App\Models\Abstracts\Model;
use App\Models\Info\Client;
use App\Models\Info\Project\Project;
use App\Models\Sheet\Credit;
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

    public function projects()
    {
        return $this->belongsToMany(Project::class);
    }

    public function getContractorSpecialityNameAttribute()
    {
        return ($contractor_speciality = $this->contractor_speciality) ? $contractor_speciality->name : "";
    }
}
