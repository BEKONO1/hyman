<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\MailTemplates;
use App\Models\Constant;
use App\Models\NotificationTemplate;

class AlterTransferMailTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(){

        $types = [
            [
                'type' => 'notification_type',
                'value' => 'transfer_booking',
                'name' => 'Transfer Booking!',
            ],
        ];

        foreach ($types as $value) {
            Constant::updateOrCreate(['type' => $value['type'], 'value' => $value['value']], $value);
        }

        
        $template = NotificationTemplate::create([
            'type' => 'transfer_booking',
            'name' => 'transfer_booking',
            'label' => 'Transfer Booking',
            'status' => 1,
            'to' => '["handyman"]',
            'channels' => ['PUSH_NOTIFICATION' => '1','IS_MAIL' => '0','IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        
        
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Transfer Booking!',
            'mail_subject' => 'Transfer Booking!',
            'whatsapp_subject' => 'Transfer Booking!',
            'sms_subject' => 'Transfer Booking!',
            'template_detail' => '<p>#[[ booking_id ]] - You have been unassigned from the booking for [[ booking_services_name ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - You have been unassigned from the booking for [[ booking_services_name ]].</p>',
            'sms_template_detail' =>'<p>#[[ booking_id ]] - You have been unassigned from the booking for [[ booking_services_name ]].</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - You have been unassigned from the booking for [[ booking_services_name ]].</p>',
        ]);
        
        $template = MailTemplates::create([
            'type' => 'transfer_booking',
            'name' => 'transfer_booking',
            'label' => 'Transfer Booking',
            'status' => 1,
            'to' => '["handyman"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);

   
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Transfer Booking!',
            'template_detail' => '<p>Hello [[ tranfer_name ]],</p>
                                  <p>You have been unassigned from the booking #[[ booking_id ]] for [[ booking_services_name ]].</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Booking Details:</strong></p>
                                  <ul>
                                  <li>Service Requested: [[ booking_services_name ]]</li>
                                  <li>Date: [[ booking_date ]]</li>
                                  <li>Time: [[ booking_time ]]</li>
                                  <li>Location: [[ venue_address ]]</li>
                                  </ul>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mail_templates', function (Blueprint $table) {
            //
        });
    }
};
