<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParcelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(! Schema::hasTable('parcels')) {
            Schema::create('parcels', function (Blueprint $table) {
                $table->increments('id');
                $table->string('tracking');
                $table->string('s_fname')->nullable();
                $table->string('s_lname')->nullable();
                $table->string('s_street')->nullable();
                $table->string('r_fname')->nullable();
                $table->string('r_lname')->nullable();
                $table->string('r_street')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('parcels');
    }
}
