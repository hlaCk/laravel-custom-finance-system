<?php

namespace App\Models\Settings;

use App\Models\Abstracts\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Permission extends Model
{
    use HasFactory;
    protected  $fillable = [
        'name', 'guard_name','group'
    ];

    protected $casts = [
        'name' => 'string',
        'guard_name' => 'string',
        'group' => 'string',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];
}
