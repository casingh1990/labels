<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sheet_configs', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('sheet_id');
            $table->bigInteger('medication_id');
            $table->integer('column');
            $table->integer('row');
            $table->float('dosage');
            $table->string('color');
            $table->boolean('black_header');
            $table->bigInteger('unit_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sheet_configs');
    }
};
