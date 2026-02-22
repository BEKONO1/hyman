<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class WalletHistoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('wallet_histories')->delete();
        
        
    }
}