<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ProviderPayoutsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('provider_payouts')->delete();
        
    }
}