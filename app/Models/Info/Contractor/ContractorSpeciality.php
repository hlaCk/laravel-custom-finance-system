<?php

namespace App\Models\Info\Contractor;

use App\Models\Abstracts\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ContractorSpeciality extends Model
{
    use HasFactory;
    use \App\Traits\HasTranslations;

    public $translatable = [];

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
//        'created_at',
//        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function contractors()
    {
        return $this->hasMany(Contractor::class);
    }
}
