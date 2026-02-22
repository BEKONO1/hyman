<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PaymentHistoriesTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('payment_histories')->delete();
    }
}