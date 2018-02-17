<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateParcelStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(! Schema::hasTable('parcel_status')) {
            Schema::create('parcel_status', function (Blueprint $table) {
                $table->integer('parcel_id')->unsigned()->nullable();
                $table->foreign('parcel_id', 'fk_p_118276_118278_status_5a81d89181e9d')->references('id')->on('parcels')->onDelete('cascade');
                $table->integer('status_id')->unsigned()->nullable();
                $table->foreign('status_id', 'fk_p_118278_118276_parcel_5a81d89181f1d')->references('id')->on('statuses')->onDelete('cascade');
                $table->string('location')->nullable();
                $table->timestamp('created_at')->useCurrent(); 

                
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
        Schema::dropIfExists('parcel_status');
    }
}
