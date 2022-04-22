<?php

namespace App\Models\Sheet;

use App\Interfaces\Sheet\ICredit;
use App\Models\Abstracts\Model;
use App\Models\Info\CreditCategory;
use App\Models\Info\Project\Project;
use App\Traits\THasBooleanStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Credit extends Model implements ICredit
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
        'date',
        'amount',
        'vat_included',
        'remarks',
        'status',
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
        'status'             => 'integer',
        'project_id'         => 'integer',
        'credit_category_id' => 'integer',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'date',
    ];

    public static function getDefaultStatus(): int
    {
        return static::ACTIVE;
    }

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
}
