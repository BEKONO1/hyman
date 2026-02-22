<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Wallet;

class WalletsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('wallets')->delete();
        
        $users = User::whereIn('user_type',['provider','handyman','user'])->get();

        foreach ($users as $user) {
            $wallet = [
                'title' => $user->display_name, // Assuming display_name is a field in your User model
                'user_id' => $user->id,
                'amount' => 0
            ];
            Wallet::create($wallet);
        }
        
        
    }
}