<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
    {
        Schema::create('provider_zone_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id');
            $table->unsignedBigInteger('zone_id');
            $table->timestamps();
            
            $table->foreign('provider_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('zone_id')->references('id')->on('service_zones')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('provider_zone_mappings');
    }
};
