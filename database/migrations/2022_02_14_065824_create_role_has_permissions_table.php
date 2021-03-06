<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoleHasPermissionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('role_id')->index('role_has_permissions_role_id_foreign');

            $table->primary([ 'permission_id', 'role_id' ]);

//            $table->foreign([ 'permission_id' ])
//                  ->references([ 'id' ])
//                  ->on('permissions')
//                  ->onDelete('CASCADE');
//            $table->foreign([ 'role_id' ])
//                  ->references([ 'id' ])
//                  ->on('roles')
//                  ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
//        Schema::table('role_has_permissions', function (Blueprint $table) {
//            $table->dropForeign('role_has_permissions_permission_id_foreign');
//            $table->dropForeign('role_has_permissions_role_id_foreign');
//        });

        Schema::dropIfExists('role_has_permissions');
    }
}
