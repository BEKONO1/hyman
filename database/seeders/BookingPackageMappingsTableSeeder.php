<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class BookingPackageMappingsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        \DB::table('booking_package_mappings')->delete();
    }
}