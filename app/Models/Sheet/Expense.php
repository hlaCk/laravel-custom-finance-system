<?php

namespace App\Models\Sheet;

use App\Interfaces\Sheet\IExpense;
use App\Models\Abstracts\Model;
use App\Models\Info\EntryCategory;
use App\Models\Info\Project\Project;
use App\Traits\THasBooleanStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model implements IExpense
{
    use HasFactory;
    use SoftDeletes;
    use THasBooleanStatus;
    use \App\Traits\HasTranslations;
    use \App\Traits\Sheet\TFormatDateAttribute;

    public $translatable = [  ];

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
        'entry_category_id',
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
        'remarks' => 'string',
        'status' => 'integer',
        'project_id' => 'integer',
        'entry_category_id' => 'integer',
        'amount' => 'double',
        'vat_included' => 'boolean',
        'date' => 'date',
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

    public function entry_category()
    {
        return $this->belongsTo(EntryCategory::class);
    }

    public function getEntryCategoryNameAttribute()
    {
        return optional($this->entry_category)->name;
    }
}
