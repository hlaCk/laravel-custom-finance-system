<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateExpensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->timestamp('date')->useCurrent();
            $table->unsignedBigInteger('project_id')->nullable();
            $table->unsignedBigInteger('entry_category_id')->nullable();
            $table->double('amount')->nullable()->default(0);
            $table->boolean('vat_included')->nullable()->default(false);
            $table->longText('remarks')->nullable();

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
        Schema::dropIfExists('expenses');
    }
}
