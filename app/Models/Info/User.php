<?php

namespace App\Models\Info;

use App\Models\Abstracts\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use HasRoles;
    use SoftDeletes;
    use \App\Traits\THasNextNewId;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'locale',
        'email_verified_at',
        'remember_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'name' => 'string',
        'email' => 'string',
        'password' => 'string',
        'email_verified_at' => 'timestamp',
        'remember_token' => 'string',
        'locale' => 'string',
    ];

    /**
     * @var array
     */
    protected $dates = [
        'created_at',
        'updated_at',
        'deleted_at',
        'email_verified_at',
    ];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->locale = $this->locale ?: currentLocale();
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes[ 'password' ] = bcrypt($value);
    }

    /**
     * A model may have multiple roles.
     */
    public function roles(): BelongsToMany
    {
        return $this->morphToMany(\App\Models\Settings\Role::class, 'model', 'model_has_roles', 'model_id', 'role_id');
    }

    /**
     * Update and save user locale.
     *
     * @param \Closure|static|null $locale
     *
     * @return self
     */
    public function updateLocale($locale = null): self
    {
        $this->locale = value($locale ?? currentLocale());
        $this->save();

        return $this;
    }

    /**
     * Change app locale by user locale if allowed.
     *
     * @return $this
     */
    public function registerLocale(): self
    {
        if( array_key_exists($this->locale, config('nova.locales')) ) {
            app()->setLocale($this->locale);
        }

        return $this;
    }
}
