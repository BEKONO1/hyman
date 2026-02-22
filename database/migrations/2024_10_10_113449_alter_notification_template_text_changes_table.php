<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\NotificationTemplate;
use Illuminate\Support\Facades\DB;
use App\Models\Constant;

class AlterNotificationTemplateTextChangesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $types = [
            [
                'type' => 'notification_type',
                'value' => 'handyman_payout',
                'name' => 'Payout Process',
            ],
        ];

        foreach ($types as $value) {
            Constant::updateOrCreate(['type' => $value['type'], 'value' => $value['value']], $value);
        }




        // Update 'provider_payout' notification template
        $provider_payout = DB::table('notification_templates')
        ->where('type', 'provider_payout')
        ->first();
    
    if ($provider_payout) {
        // Update the 'to' field for the provider payout template
        DB::table('notification_templates')
            ->where('type', 'provider_payout')
            ->update(['to' => '["provider","admin"]']);
    
        // Proceed to update the content mappings
        DB::table('notification_template_content_mapping')
            ->where('template_id', $provider_payout->id)
            ->where('user_type', 'provider')
            ->update([
                'user_type' => 'provider',
                'subject' => 'Payout Received',
                'template_detail' => '<p>Your payout of [[ amount ]] has been received.</p>',
            ]);
    
        DB::table('notification_template_content_mapping')
            ->where('template_id', $provider_payout->id)
            ->where('user_type', 'handyman')
            ->update([
                'user_type' => 'admin',
                'subject' => 'Payout Processed',
                'template_detail' => '<p>Payout of [[ amount ]] has been processed to [[ provider_name ]].</p>',
            ]);
    }
        // Update 'payment_message_status' template if it exists
        $payment_message_status = DB::table('notification_templates')
            ->where('type', 'payment_message_status')
            ->first();

        if ($payment_message_status) {
            DB::table('notification_template_content_mapping')
                ->where('template_id', $payment_message_status->id)
                ->where('user_type', 'provider')
                ->update([
                    'template_detail' => '<p>#[[ booking_id ]] - Payment For [[ booking_services_name ]] changed to [[ payment_status ]].</p>',
                ]);
        }

        // Update 'subscription_add' template if it exists
        $subscription_add = DB::table('notification_templates')
            ->where('type', 'subscription_add')
            ->first();

        if ($subscription_add) {
            DB::table('notification_template_content_mapping')
                ->where('template_id', $subscription_add->id)
                ->where('user_type', 'admin')
                ->update([
                    'template_detail' => '<p>[[ provider_name ]] has subscribed to a new [[ plan_name ]].</p>',
                ]);
        }


        $template = NotificationTemplate::create([
            'type' => 'handyman_payout',
            'name' => 'handyman_payout',
            'label' => 'Handyman Payout',
            'status' => 1,
            'to' => '["provider","handyman"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Payout Received',
            'template_detail' => '<p>Your payout of [[ amount ]] has been received from [[ provider_name ]].</p>',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Payout Processed',
            'template_detail' => '<p>Payout of [[ amount ]] has been processed to [[ handyman_name ]].</p>',
        ]);
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
