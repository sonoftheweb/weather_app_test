<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWeathersTempraturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('weathers_temperatures', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('weather_id');
            $table->unsignedBigInteger('location_id');
            $table->decimal('temperature', 3, 1);
            $table->timestamps();

            $table->foreign('location_id')->references('id')->on('weather_locations')
                ->onDelete('cascade');
            $table->foreign('weather_id')->references('id')->on('weathers')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('weathers_temperatures');
    }
}
