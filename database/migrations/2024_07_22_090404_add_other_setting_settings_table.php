<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Setting;
use App\Models\AppSetting;
use App\Models\AppDownload;

class AddOtherSettingSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        
        $settings = Setting::all();
        foreach ($settings as $setting) {

            if ($setting->key == 'OTHER_SETTING') {
                $setting->update([
                    'value' => '{"social_login":1,"google_login":1,"apple_login":1,"otp_login":1,"online_payment":1,"blog":1,"maintenance_mode":0, "advanced_payment_setting":1,"wallet":1,"enable_chat_gpt":1,"test_without_key":1,"chat_gpt_key":null,"force_update_user_app":0,"user_app_minimum_version":null,"user_app_latest_version":null,"force_update_provider_app":0,"provider_app_minimum_version":null,"provider_app_latest_version":null,"force_update_admin_app":0,"admin_app_minimum_version":null,"admin_app_latest_version":null,"firebase_notification":1,"project_id":"Project ID","auto_assign_provider":0,"dashboard_type":"dashboard"}',
                ]);
            }
            else{
                Setting::firstOrCreate([
                    'type' => 'OTHER_SETTING',
                    'key' => 'OTHER_SETTING',
                ], [
                    'value' =>  '{"social_login":1,"google_login":1,"apple_login":1,"otp_login":1,"online_payment":1,"blog":1,"maintenance_mode":0, "advanced_payment_setting":1,"wallet":1,"enable_chat_gpt":1,"test_without_key":1,"chat_gpt_key":null,"force_update_user_app":0,"user_app_minimum_version":null,"user_app_latest_version":null,"force_update_provider_app":0,"provider_app_minimum_version":null,"provider_app_latest_version":null,"force_update_admin_app":0,"admin_app_minimum_version":null,"admin_app_latest_version":null,"firebase_notification":1,"project_id":"Project ID","auto_assign_provider":0,"dashboard_type":"dashboard"}',
                ]);
            }
           
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('settings', function (Blueprint $table) {
            //
        });
    }
}
