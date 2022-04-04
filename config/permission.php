<?php

return [

    'models' => [

        /*
         * When using the "HasPermissions" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your permissions. Of course, it
         * is often just the "Permission" model but you may use whatever you like.
         *
         * The model you want to use as a Permission model needs to implement the
         * `Spatie\Permission\Contracts\Permission` contract.
         */

        'permission' => Spatie\Permission\Models\Permission::class,

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * Eloquent model should be used to retrieve your roles. Of course, it
         * is often just the "Role" model but you may use whatever you like.
         *
         * The model you want to use as a Role model needs to implement the
         * `Spatie\Permission\Contracts\Role` contract.
         */

        'role' => Spatie\Permission\Models\Role::class,

    ],

    'table_names' => [

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your roles. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */

        'roles' => 'roles',

        /*
         * When using the "HasPermissions" trait from this package, we need to know which
         * table should be used to retrieve your permissions. We have chosen a basic
         * default value but you may easily change it to any table you like.
         */

        'permissions' => 'permissions',

        /*
         * When using the "HasPermissions" trait from this package, we need to know which
         * table should be used to retrieve your models permissions. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'model_has_permissions' => 'model_has_permissions',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your models roles. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'model_has_roles' => 'model_has_roles',

        /*
         * When using the "HasRoles" trait from this package, we need to know which
         * table should be used to retrieve your roles permissions. We have chosen a
         * basic default value but you may easily change it to any table you like.
         */

        'role_has_permissions' => 'role_has_permissions',
    ],

    'column_names' => [
        /*
         * Change this if you want to name the related pivots other than defaults
         */
        'role_pivot_key' => null, //default 'role_id',
        'permission_pivot_key' => null, //default 'permission_id',

        /*
         * Change this if you want to name the related model primary key other than
         * `model_id`.
         *
         * For example, this would be nice if your primary keys are all UUIDs. In
         * that case, name this `model_uuid`.
         */

        'model_morph_key' => 'model_id',

        /*
         * Change this if you want to use the teams feature and your related model's
         * foreign key is other than `team_id`.
         */

        'team_foreign_key' => 'team_id',
    ],

    /*
     * When set to true the package implements teams using the 'team_foreign_key'. If you want
     * the migrations to register the 'team_foreign_key', you must set this to true
     * before doing the migration. If you already did the migration then you must make a new
     * migration to also add 'team_foreign_key' to 'roles', 'model_has_roles', and
     * 'model_has_permissions'(view the latest version of package's migration file)
     */

    'teams' => false,

    /*
     * When set to true, the required permission names are added to the exception
     * message. This could be considered an information leak in some contexts, so
     * the default setting is false here for optimum safety.
     */

    'display_permission_in_exception' => false,

    /*
     * When set to true, the required role names are added to the exception
     * message. This could be considered an information leak in some contexts, so
     * the default setting is false here for optimum safety.
     */

    'display_role_in_exception' => false,

    /*
     * By default wildcard permission lookups are disabled.
     */

    'enable_wildcard_permission' => false,

    'cache' => [

        /*
         * By default all permissions are cached for 24 hours to speed up performance.
         * When permissions or roles are updated the cache is flushed automatically.
         */

        'expiration_time' => \DateInterval::createFromDateString('24 hours'),

        /*
         * The cache key used to store all permissions.
         */

        'key' => 'spatie.permission.cache',

        /*
         * You may optionally indicate a specific cache driver to use for permission and
         * role caching using any of the `store` drivers listed in the cache.php config
         * file. Using 'default' here means to use the `default` set in cache.php.
         */

        'store' => 'default',
    ],

    'permissions' => [
        [ 'name' => 'account.view_any', 'group' => 'account', 'guard_name' => 'web' ],
        [ 'name' => 'account.view', 'group' => 'account', 'guard_name' => 'web' ],
        [ 'name' => 'account.create', 'group' => 'account', 'guard_name' => 'web' ],
        [ 'name' => 'account.edit', 'group' => 'account', 'guard_name' => 'web' ],
        [ 'name' => 'account.delete', 'group' => 'account', 'guard_name' => 'web' ],
        [ 'name' => 'account.restore', 'group' => 'account', 'guard_name' => 'web' ],
        [ 'name' => 'account.force_delete', 'group' => 'account', 'guard_name' => 'web' ],

        [ 'name' => 'client.view_any', 'group' => 'client', 'guard_name' => 'web' ],
        [ 'name' => 'client.view', 'group' => 'client', 'guard_name' => 'web' ],
        [ 'name' => 'client.create', 'group' => 'client', 'guard_name' => 'web' ],
        [ 'name' => 'client.edit', 'group' => 'client', 'guard_name' => 'web' ],
        [ 'name' => 'client.delete', 'group' => 'client', 'guard_name' => 'web' ],
        [ 'name' => 'client.restore', 'group' => 'client', 'guard_name' => 'web' ],
        [ 'name' => 'client.force_delete', 'group' => 'client', 'guard_name' => 'web' ],

        [ 'name' => 'setting.view_any', 'group' => 'setting', 'guard_name' => 'web' ],
        [ 'name' => 'setting.view', 'group' => 'setting', 'guard_name' => 'web' ],
        [ 'name' => 'setting.create', 'group' => 'setting', 'guard_name' => 'web' ],
        [ 'name' => 'setting.edit', 'group' => 'setting', 'guard_name' => 'web' ],
        [ 'name' => 'setting.delete', 'group' => 'setting', 'guard_name' => 'web' ],
        [ 'name' => 'setting.restore', 'group' => 'setting', 'guard_name' => 'web' ],
        [ 'name' => 'setting.force_delete', 'group' => 'setting', 'guard_name' => 'web' ],

        [ 'name' => 'entry_category.view_any', 'group' => 'entry_category', 'guard_name' => 'web' ],
        [ 'name' => 'entry_category.view', 'group' => 'entry_category', 'guard_name' => 'web' ],
        [ 'name' => 'entry_category.create', 'group' => 'entry_category', 'guard_name' => 'web' ],
        [ 'name' => 'entry_category.edit', 'group' => 'entry_category', 'guard_name' => 'web' ],
        [ 'name' => 'entry_category.delete', 'group' => 'entry_category', 'guard_name' => 'web' ],
        [ 'name' => 'entry_category.restore', 'group' => 'entry_category', 'guard_name' => 'web' ],
        [ 'name' => 'entry_category.force_delete', 'group' => 'entry_category', 'guard_name' => 'web' ],

        [ 'name' => 'project.view_any', 'group' => 'project', 'guard_name' => 'web' ],
        [ 'name' => 'project.view', 'group' => 'project', 'guard_name' => 'web' ],
        [ 'name' => 'project.create', 'group' => 'project', 'guard_name' => 'web' ],
        [ 'name' => 'project.edit', 'group' => 'project', 'guard_name' => 'web' ],
        [ 'name' => 'project.delete', 'group' => 'project', 'guard_name' => 'web' ],
        [ 'name' => 'project.restore', 'group' => 'project', 'guard_name' => 'web' ],
        [ 'name' => 'project.force_delete', 'group' => 'project', 'guard_name' => 'web' ],

        [ 'name' => 'project_status.view_any', 'group' => 'project_status', 'guard_name' => 'web' ],
        [ 'name' => 'project_status.view', 'group' => 'project_status', 'guard_name' => 'web' ],
        [ 'name' => 'project_status.create', 'group' => 'project_status', 'guard_name' => 'web' ],
        [ 'name' => 'project_status.edit', 'group' => 'project_status', 'guard_name' => 'web' ],
        [ 'name' => 'project_status.delete', 'group' => 'project_status', 'guard_name' => 'web' ],
        [ 'name' => 'project_status.restore', 'group' => 'project_status', 'guard_name' => 'web' ],
        [ 'name' => 'project_status.force_delete', 'group' => 'project_status', 'guard_name' => 'web' ],

        [ 'name' => 'expense.view_any', 'group' => 'expense', 'guard_name' => 'web' ],
        [ 'name' => 'expense.view', 'group' => 'expense', 'guard_name' => 'web' ],
        [ 'name' => 'expense.create', 'group' => 'expense', 'guard_name' => 'web' ],
        [ 'name' => 'expense.edit', 'group' => 'expense', 'guard_name' => 'web' ],
        [ 'name' => 'expense.delete', 'group' => 'expense', 'guard_name' => 'web' ],
        [ 'name' => 'expense.restore', 'group' => 'expense', 'guard_name' => 'web' ],
        [ 'name' => 'expense.force_delete', 'group' => 'expense', 'guard_name' => 'web' ],

//        [ 'name' => 'example.view_any', 'group' => 'example', 'guard_name' => 'web' ],
//        [ 'name' => 'example.view', 'group' => 'example', 'guard_name' => 'web' ],
//        [ 'name' => 'example.create', 'group' => 'example', 'guard_name' => 'web' ],
//        [ 'name' => 'example.edit', 'group' => 'example', 'guard_name' => 'web' ],
//        [ 'name' => 'example.delete', 'group' => 'example', 'guard_name' => 'web' ],
//        [ 'name' => 'example.restore', 'group' => 'example', 'guard_name' => 'web' ],
//        [ 'name' => 'example.force_delete', 'group' => 'example', 'guard_name' => 'web' ],
    ],
];
