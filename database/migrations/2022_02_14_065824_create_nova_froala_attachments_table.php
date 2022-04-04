<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNovaFroalaAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nova_froala_attachments', function (Blueprint $table) {
            $table->increments('id');
            $table->string('attachable_type', 250)->nullable();
            $table->integer('attachable_id')->nullable();
            $table->string('attachment', 250)->nullable();
            $table->string('disk', 250)->nullable();
            $table->text('url')->nullable();
            $table->timestamp('created_at')->nullable()->useCurrent();
            $table->timestamp('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nova_froala_attachments');
    }
}
