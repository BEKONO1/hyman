<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\NotificationTemplate;
use Illuminate\Support\Facades\DB;
use App\Models\Constant;

class AlterHelpdeskNotificationTemplateTable extends Migration
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
                'value' => 'add_helpdesk',
                'name' => 'New Query Received!',
            ],
            [
                'type' => 'notification_type',
                'value' => 'closed_helpdesk',
                'name' => 'Query Closed Received!',
            ],
            [
                'type' => 'notification_type',
                'value' => 'reply_helpdesk',
                'name' => 'Query Replied!',
            ],
            [
                'type' => 'notification_type',
                'value' => 'cancellation_charges',
                'name' => 'Cancellation Charges',
            ],
        ];

        foreach ($types as $value) {
            Constant::updateOrCreate(['type' => $value['type'], 'value' => $value['value']], $value);
        }

        $template = NotificationTemplate::create([
            'type' => 'add_helpdesk',
            'name' => 'add_helpdesk',
            'label' => 'Query confirmation',
            'status' => 1,
            'to' => '["admin"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'New Query Received',
            'template_detail' => '<p>New Query [[ sender_name ]] - [[ subject ]].</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'closed_helpdesk',
            'name' => 'closed_helpdesk',
            'label' => 'Closed',
            'status' => 1,
            'to' => '["admin","provider","handyman","user"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Query Closed',
            'template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Query Closed',
            'template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Query Closed',
            'template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Query Closed',
            'template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'reply_helpdesk',
            'name' => 'reply_helpdesk',
            'label' => 'Replied Query',
            'status' => 1,
            'to' => '["admin","provider","handyman","user"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Query Replied',
            'template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Query Replied',
            'template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Query Replied',
            'template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Query Replied',
            'template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
        ]);
        $template = NotificationTemplate::create([
            'type' => 'cancellation_charges',
            'name' => 'cancellation_charges',
            'label' => 'Cancellation Charges',
            'status' => 1,
            'to' => '["admin","user"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Cancellation Charges',
            'template_detail' => `<p>#[[ booking_id ]] - A cancellation charge of [[ paid_amount ]] has been deducted from the customer's wallet.</p>`,
        ]);
       
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Cancellation Charges',
            'template_detail' => '<p>#[[ booking_id ]] - A cancellation charge [[ paid_amount ]] has been deducted from your wallet.</p>',
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
