<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BookingHandymanMappingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('booking_handyman_mappings')->delete();
    }
}