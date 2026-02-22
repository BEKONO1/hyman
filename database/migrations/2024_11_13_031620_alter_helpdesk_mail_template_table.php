<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use App\Models\MailTemplates;

class AlterHelpdeskMailTemplateTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $template = MailTemplates::create([
            'type' => 'add_helpdesk',
            'name' => 'add_helpdesk',
            'label' => 'New Query',
            'status' => 1,
            'to' => '["admin"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'New Query Received',
            'template_detail' => '<p>Hello [[ admin_name ]],</p>
                                  <p>Below are the help desk details for a new query request received from a customer.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Help Desk Details:</strong></p>
                                  <ul>
                                  <li>Customer Name: [[ sender_name ]]</li>
                                  <li>Help Desk ID: #[[ helpdesk_id ]]</li>
                                  <li>Subject: [[ subject ]]</li>
                                  </ul>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);

        $template = MailTemplates::create([
            'type' => 'closed_helpdesk',
            'name' => 'closed_helpdesk',
            'label' => 'Closed Query',
            'status' => 1,
            'to' => '["admin","provider","handyman","user"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Query Closed',
            'template_detail' => '<p>Hello [[ receiver_name ]],</p>
                                  <p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Help Desk Details:</strong></p>
                                  <ul>
                                  <li>Customer Name: [[ sender_name ]]</li>
                                  <li>Help Desk ID: #[[ helpdesk_id ]]</li>
                                  </ul>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Query Closed',
            'template_detail' => '<p>Hello [[ receiver_name ]],</p>
                                  <p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Help Desk Details:</strong></p>
                                  <ul>
                                  <li>Customer Name: [[ sender_name ]]</li>
                                  <li>Help Desk ID: #[[ helpdesk_id ]]</li>
                                  </ul>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Query Closed',
            'template_detail' => '<p>Hello [[ receiver_name ]],</p>
                                  <p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Help Desk Details:</strong></p>
                                  <ul>
                                  <li>Customer Name: [[ sender_name ]]</li>
                                  <li>Help Desk ID: #[[ helpdesk_id ]]</li>
                                  </ul>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Query Closed',
            'template_detail' => '<p>Hello [[ receiver_name ]],</p>
                                  <p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Help Desk Details:</strong></p>
                                  <ul>
                                  <li>Customer Name: [[ sender_name ]]</li>
                                  <li>Help Desk ID: #[[ helpdesk_id ]]</li>
                                  </ul>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);

        $template = MailTemplates::create([
            'type' => 'reply_helpdesk',
            'name' => 'reply_helpdesk',
            'label' => 'Replied Query',
            'status' => 1,
            'to' => '["admin","provider","handyman","user"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Query Replied',
            'template_detail' => '<p>Hello [[ receiver_name ]],</p>
                                  <p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Help Desk Details:</strong></p>
                                  <ul>
                                  <li>Customer Name: [[ sender_name ]]</li>
                                  <li>Help Desk ID: #[[ helpdesk_id ]]</li>
                                  </ul>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Query Replied',
            'template_detail' => '<p>Hello [[ receiver_name ]],</p>
                                  <p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Help Desk Details:</strong></p>
                                  <ul>
                                  <li>Customer Name: [[ sender_name ]]</li>
                                  <li>Help Desk ID: #[[ helpdesk_id ]]</li>
                                  </ul>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Query Replied',
            'template_detail' => '<p>Hello [[ receiver_name ]],</p>
                                  <p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Help Desk Details:</strong></p>
                                  <ul>
                                  <li>Customer Name: [[ sender_name ]]</li>
                                  <li>Help Desk ID: #[[ helpdesk_id ]]</li>
                                  </ul>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Query Replied',
            'template_detail' => '<p>Hello [[ receiver_name ]],</p>
                                  <p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Help Desk Details:</strong></p>
                                  <ul>
                                  <li>Customer Name: [[ sender_name ]]</li>
                                  <li>Help Desk ID: #[[ helpdesk_id ]]</li>
                                  </ul>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template = MailTemplates::create([
            'type' => 'cancellation_charges',
            'name' => 'cancellation_charges',
            'label' => 'Cancellation Charges',
            'status' => 1,
            'to' => '["admin","user"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Cancellation Charges',
            'template_detail' => `<p>Hello [[ admin_name ]],</p>
                                  <p>We would like to inform you that the service provided by [[ provider_name ]] to [[ customer_name ]] has been cancelled. Consequently, a cancellation charge of [[ paid_amount ]] has been deducted from the customer's wallet.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>`,
        ]);
        
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Cancellation Charges',
            'template_detail' => '<p>Hello [[ customer_name ]],</p>
                                  <p>We would like to inform you that the service provided by [[ provider_name ]] to you has been cancelled. Consequently, a cancellation charge [[ paid_amount ]] has been deducted from your wallet.</p>
                                  <p>If you have any further questions or concerns, please dont hesitate to contact us.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mail_templates', function (Blueprint $table) {
            //
        });
    }
}
