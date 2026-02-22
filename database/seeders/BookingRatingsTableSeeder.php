<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BookingRatingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('booking_ratings')->delete();
    }
}