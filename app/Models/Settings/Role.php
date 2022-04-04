<?php

namespace App\Models\Settings;

use App\Models\Abstracts\Model;
use App\Models\Info\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;

    protected $fillable = [ 'id', 'name', 'guard_name' ];

    protected $casts = [
        'name'       => 'string',
        'guard_name' => 'string',
    ];

    protected $dates = [
        'created_at',
        'updated_at',
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_has_permissions');
    }

    public function users()
    {
        return $this->morphedByMany(User::class, 'model', 'model_has_roles', 'role_id', 'model_id');
    }

}
