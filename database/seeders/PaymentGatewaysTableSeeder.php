<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class PaymentGatewaysTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('payment_gateways')->delete();
        
        \DB::table('payment_gateways')->insert(array (
            0 => 
            array (
                'created_at' => '2023-09-05 08:25:47',
                'id' => 1,
                'is_test' => 0,
                'live_value' => NULL,
                'status' => 1,
                'title' => 'Cash on Delivery',
                'type' => 'cash',
                'updated_at' => '2023-09-05 08:25:47',
                'value' => NULL,
            ),
            1 => 
           array (
                'created_at' => '2023-09-05 08:26:22',
                'id' => 2,
                'is_test' => 1,
                'live_value' => NULL,
                'status' => 1,
                'title' => 'Stripe Payment',
                'type' => 'stripe',
                'updated_at' => '2023-09-05 08:26:22',
                'value' => json_encode([
                    "stripe_url" => "",
                    "stripe_key" => "",
                    "stripe_publickey" => ""
                ])
            ),
            2 => 
            array (
                'created_at' => '2023-09-05 09:49:30',
                'id' => 3,
                'is_test' => 1,
                'live_value' => NULL,
                'status' => 1,
                'title' => 'Razor Pay',
                'type' => 'razorPay',
                'updated_at' => '2023-09-05 09:49:30',
               'value' => json_encode([
                    "razor_url" => "",
                    "razor_key" => "",
                    "razor_secret" => ""
                ]),
            ),
            3 => 
            array (
                'created_at' => '2023-09-05 09:50:14',
                'id' => 4,
                'is_test' => 1,
                'live_value' => NULL,
                'status' => 1,
                'title' => 'FlutterWave',
                'type' => 'flutterwave',
                'updated_at' => '2023-09-05 09:50:14',
                'value' => json_encode([
                    "flutterwave_url" => "",
                    "flutterwave_public" => "",
                    "flutterwave_secret" => "",
                    "flutterwave_encryption" => ""
                ]),
            ),
        ));
        
        
    }
}
