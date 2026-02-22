<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AlterProviderHandymanTypeValue extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('provider_types')
            ->where('type', 'percentage')  
            ->update(['type' => 'percent']);
            
        DB::table('handyman_types')
            ->where('type', 'percentage')  
            ->update(['type' => 'percent']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
