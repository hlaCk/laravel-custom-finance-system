<?php

namespace App\Policies\Abstracts;

use App\Models\Info\User;
use Illuminate\Auth\Access\HandlesAuthorization;
use Illuminate\Database\Eloquent\Model;

/**
 *
 */
class BasePolicy
{
    use HandlesAuthorization;

    public static string $permission_name;
    // public static bool $hideFromNavigation;

    /**
     * Determine whether the user can view any models.
     *
     * @param \App\Models\Info\User $user
     *
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return static::userCan("view_any", $user);
    }

    /**
     * Determine whether the user can view the model.
     *
     * @param \App\Models\Info\User|null               $user
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return mixed
     */
    public function view(?User $user, Model $model)
    {
        return static::userCan("view", $user);
    }

    /**
     * Determine whether the user can create models.
     *
     * @param \App\Models\Info\User $user
     *
     * @return mixed
     */
    public function create(User $user)
    {
        return static::userCan("create", $user);
    }

    /**
     * Determine whether the user can update the model.
     *
     * @param \App\Models\Info\User                    $user
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return mixed
     */
    public function update(User $user, Model $model)
    {
        return static::userCan("edit", $user);
    }

    /**
     * Determine whether the user can delete the model.
     *
     * @param \App\Models\Info\User                    $user
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return mixed
     */
    public function delete(User $user, Model $model)
    {
        return static::userCan("delete", $user);
    }

    /**
     * Determine whether the user can restore the model.
     *
     * @param \App\Models\Info\User                    $user
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return mixed
     */
    public function restore(User $user, Model $model)
    {
        return static::userCan("restore", $user);
    }

    /**
     * Determine whether the user can permanently delete the model.
     *
     * @param \App\Models\Info\User                    $user
     * @param \Illuminate\Database\Eloquent\Model $model
     *
     * @return mixed
     */
    public function forceDelete(User $user, Model $model)
    {
        return false; // static::userCan("force_delete", $user);
    }

    /**
     * @return false
     */
    // public function availableForNavigation(User $user = null)
    // {
    //     $user ??= request()->user();
    //     if( static::hideFromNavigation() ) {
    //         return false;
    //     }
    //
    //     return static::userCan('index');
    // }

    /**
     * @return false
     */
    // private static function hideFromNavigation()
    // {
    //     return static::$hideFromNavigation ?? false;
    // }

    public static function getPermissionName(string $permission): string
    {
        if( is_null($permission_name = static::$permission_name ?? null) ) {
            return $permission;
        }

        return "{$permission_name}.$permission";
    }

    public static function userCan(string $permission, ?User $user = null): bool
    {
        if( !$user ) {
            return false;
        }

        return $user->can(static::getPermissionName($permission));
    }

}
