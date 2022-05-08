<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateContractorProjectTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('contractor_project', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('contractor_id');
            $table->unsignedBigInteger('project_id');

            $table->timestamp('date')->useCurrent()->nullable();
            $table->longText('remarks')->nullable();
            $table->string('unit')->nullable();
            $table->unsignedDouble('quantity');
            $table->unsignedDouble('price');
//            $table->unsignedDouble('total');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('contractor_project');
    }
}
