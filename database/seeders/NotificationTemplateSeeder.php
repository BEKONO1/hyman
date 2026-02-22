<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Constant;
use App\Models\NotificationTemplate;


class NotificationTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Disable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        /*
         * NotificationTemplates Seed
         * ------------------
         */

        // DB::table('notificationtemplates')->truncate();
        // echo "Truncate: notificationtemplates \n";

        $types = [
            [
                'type' => 'notification_type',
                'value' => 'add_booking',
                'name' => 'New Service Booking Received!',
            ],
            [
                'type' => 'notification_type',
                'value' => 'assigned_booking',
                'name' => 'Booking Assigned!',
            ],
            [
                'type' => 'notification_type',
                'value' => 'tranfered_booking',
                'name' => 'Booking Assigned!',
            ],
            [
                'type' => 'notification_type',
                'value' => 'update_booking_status',
                'name' => 'Update Booking',
            ],
            [
                'type' => 'notification_type',
                'value' => 'cancel_booking',
                'name' => 'Cancel On Booking',
            ],
            [
                'type' => 'notification_type',
                'value' => 'payment_message_status',
                'name' => 'Payment Message Status',
            ],

            [
                'type' => 'notification_type',
                'value' => 'wallet_payout_transfer',
                'name' => 'Wallet Payout Transfer',
            ],
            [
                'type' => 'notification_type',
                'value' => 'wallet_top_up',
                'name' => 'Wallet Topped Up! New Balance Available',
            ],
            [
                'type' => 'notification_type',
                'value' => 'wallet_refund',
                'name' => 'Wallet Refund',
            ],
            [
                'type' => 'notification_type',
                'value' => 'paid_with_wallet',
                'name' => 'Paid For Booking',
            ],
            [
                'type' => 'notification_type',
                'value' => 'job_requested',
                'name' => ' New Custom Job Request',
            ],
            [
                'type' => 'notification_type',
                'value' => 'provider_send_bid',
                'name' => 'Provider Send Bid',
            ],
            [
                'type' => 'notification_type',
                'value' => 'user_accept_bid',
                'name' => 'User Accept Bid',
            ],

            [
                'type' => 'notification_type',
                'value' => 'provider_payout',
                'name' => 'Payout Process',
            ],
            [
                'type' => 'notification_type',
                'value' => 'handyman_payout',
                'name' => 'Payout Process',
            ],


            [
                'type' => 'notification_type',
                'value' => 'subscription_add',
                'name' => 'Subscription Add',
            ],
            [
                'type' => 'notification_type',
                'value' => 'service_request',
                'name' => 'Service Request',
            ],
            [
                'type' => 'notification_type',
                'value' => 'service_request_approved',
                'name' => 'Service Approved',
            ],
            [
                'type' => 'notification_type',
                'value' => 'service_request_reject',
                'name' => 'Service Reject',
            ],
            // [
            //     'type' => 'notification_type',
            //     'value' => 'subscription_reminder',
            //     'name' => 'Subscription Reminder',
            // ],
            [
                'type' => 'notification_type',
                'value' => 'register',
                'name' => 'Register',
            ],
            [
                'type' => 'notification_type',
                'value' => 'withdraw_money',
                'name' => 'Withdraw Money',
            ],
            // [
            //     'type' => 'notification_type',
            //     'value' => 'forget_password',
            //     'name' => 'Forget Email/Password',
            // ],
            // [
            //     'type' => 'notification_param_button',
            //     'value' => 'id',
            //     'name' => 'ID',
            // ],


            [
                'type' => 'notification_param_button',
                'value' => 'customer_name',
                'name' => 'Customer Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'admin_name',
                'name' => 'Admin Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'provider_name',
                'name' => 'Provider Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'handyman_name',
                'name' => 'Handyman Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'booking_id',
                'name' => 'Booking ID',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'booking_services_name',
                'name' => 'Booking Services Name',
            ],

            [
                'type' => 'notification_param_button',
                'value' => 'booking_date',
                'name' => 'Booking Date',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'booking_time',
                'name' => 'Booking Time',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'venue_address',
                'name' => 'Venue / Address',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'booking_status',
                'name' => 'Booking Status',
            ],

            [
                'type' => 'notification_param_button',
                'value' => 'cancelled_user_name',
                'name' => 'Cancelled User Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'payment_status',
                'name' => 'Payment Status',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'company_contact_info',
                'name' => 'Company Info',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'company_name',
                'name' => 'Company Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'credit_debit_amount',
                'name' => 'Wallet Credit/Debit Amnount',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'pay_amount',
                'name' => 'Pay Amount',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'wallet_transaction_id',
                'name' => 'wallet Transaction ID',
            ],

            [
                'type' => 'notification_param_button',
                'value' => 'wallet_transaction_type',
                'name' => 'wallet Transaction Type',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'wallet_amount',
                'name' => 'wallet Amount',
            ],


            [
                'type' => 'notification_param_button',
                'value' => 'refund_amount',
                'name' => 'Refund Amount',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'amount',
                'name' => 'Amount',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'job_id',
                'name' => 'Job ID',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'job_name',
                'name' => 'Job Name',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'job_description',
                'name' => 'Job Description',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'bid_amount',
                'name' => 'Bid Amount',
            ],

            [
                'type' => 'notification_param_button',
                'value' => 'job_price',
                'name' => 'Job Price',
            ],

            [
                'type' => 'notification_param_button',
                'value' => 'plan_name',
                'name' => 'Subscription Plan Name',
            ],

            [
                'type' => 'notification_param_button',
                'value' => 'start_date',
                'name' => 'Start Date',
            ],

            [
                'type' => 'notification_param_button',
                'value' => 'end_date',
                'name' => 'End Date',
            ],

            [
                'type' => 'notification_param_button',
                'value' => 'user_name',
                'name' => 'User Name',
            ],

            [
                'type' => 'notification_param_button',
                'value' => 'banner_title',
                'name' => 'Banner Title',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'user_email',
                'name' => 'User Email',
            ],
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
            [
                'type' => 'notification_type',
                'value' => 'promotional_banner',
                'name' => 'Promotional Banner Created',
            ],
            [
                'type' => 'notification_type',
                'value' => 'promotional_banner_accepted',
                'name' => 'Promotional Banner Accepted',
            ],
            [
                'type' => 'notification_type',
                'value' => 'promotional_banner_rejected',
                'name' => 'Promotional Banner Rejected',
            ],
            [
                'type' => 'notification_type',
                'value' => 'wallet_refund_promotional_banner',
                'name' => 'Wallet Refund Initiated for Promotional Banner',
            ],




            ////////////////////////////////////////////////////////////////////////////////////////////////////////////





            [
                'type' => 'notification_to',
                'value' => 'user',
                'name' => 'User',
            ],

            [
                'type' => 'notification_to',
                'value' => 'provider',
                'name' => 'Provider',
            ],
            [
                'type' => 'notification_to',
                'value' => 'handyman',
                'name' => 'Handyman',
            ],

            [
                'type' => 'notification_to',
                'value' => 'demo_admin',
                'name' => 'Demo Admin',
            ],
            [
                'type' => 'notification_to',
                'value' => 'admin',
                'name' => 'Admin',
            ],
        ];

        foreach ($types as $value) {
            Constant::updateOrCreate(['type' => $value['type'], 'value' => $value['value']], $value);
        }

        // echo " Insert: notificationtempletes \n\n";

        // Enable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('notification_templates')->delete();
        DB::table('notification_template_content_mapping')->delete();

        $template = NotificationTemplate::create([
            'type' => 'add_booking',
            'name' => 'add_booking',
            'label' => 'Booking confirmation',
            'status' => 1,
            'to' => '["admin","provider"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'New Booking Received',
            'mail_subject' => 'New Booking Received',
            'whatsapp_subject' => 'New Booking Received',
            'sms_subject' => 'New Booking Received',
            'template_detail' => '<p>New booking #[[ booking_id ]] - [[ customer_name ]] has booked [[ booking_services_name ]].</p>',
            'whatsapp_template_detail' => '<p>New booking #[[ booking_id ]] - [[ customer_name ]] has booked [[ booking_services_name ]].</p>',
            'sms_template_detail' => '<p>New booking #[[ booking_id ]] - [[ customer_name ]] has booked [[ booking_services_name ]].</p>',
            'mail_template_detail' => '<p>New booking #[[ booking_id ]] - [[ customer_name ]] has booked [[ booking_services_name ]].</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'New Booking Received',
            'mail_subject' => 'New Booking Received',
            'whatsapp_subject' => 'New Booking Received',
            'sms_subject' => 'New Booking Received',
            'template_detail' => '<p>New booking #[[ booking_id ]] - [[ customer_name ]] has booked [[ booking_services_name ]].</p>',
            'whatsapp_template_detail' => '<p>New booking #[[ booking_id ]] - [[ customer_name ]] has booked [[ booking_services_name ]].</p>',
            'sms_template_detail' => '<p>New booking #[[ booking_id ]] - [[ customer_name ]] has booked [[ booking_services_name ]].</p>',
            'mail_template_detail' => '<p>New booking #[[ booking_id ]] - [[ customer_name ]] has booked [[ booking_services_name ]].</p>',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Booking Assigned!',
            'mail_subject' => 'Booking Assigned!',
            'whatsapp_subject' => 'Booking Assigned!',
            'sms_subject' => 'Booking Assigned!',
            'template_detail' => '<p>#[[ booking_id ]] - You have been assigned to provide [[ booking_services_name ]]. Please proceed accordingly.</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - You have been assigned to provide [[ booking_services_name ]]. Please proceed accordingly.</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - You have been assigned to provide [[ booking_services_name ]]. Please proceed accordingly.</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - You have been assigned to provide [[ booking_services_name ]]. Please proceed accordingly.</p>',

        ]);

        $template = NotificationTemplate::create([
            'type' => 'assigned_booking',
            'name' => 'assigned_booking',
            'label' => 'Booking Assigned',
            'status' => 1,
            'to' => '["handyman","user","provider"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Booking Assigned!',
            'mail_subject' => 'Booking Assigned!',
            'whatsapp_subject' => 'Booking Assigned!',
            'sms_subject' => 'Booking Assigned!',
            'template_detail' => '<p>#[[ booking_id ]] - You have been assigned to provide [[ booking_services_name ]]. Please proceed accordingly.</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - You have been assigned to provide [[ booking_services_name ]]. Please proceed accordingly.</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - You have been assigned to provide [[ booking_services_name ]]. Please proceed accordingly.</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - You have been assigned to provide [[ booking_services_name ]]. Please proceed accordingly.</p>',

        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Booking Assigned!',
            'whatsapp_subject' => 'Booking Assigned!',
            'sms_subject' => 'Booking Assigned!',
            'mail_subject' => 'Booking Assigned!',
            'template_detail' => '<p>#[[ booking_id ]] - [[ assignee_name ]] has been assigned to [[ booking_services_name ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - [[ assignee_name ]] has been assigned to [[ booking_services_name ]].</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - [[ assignee_name ]] has been assigned to [[ booking_services_name ]].</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - [[ assignee_name ]] has been assigned to [[ booking_services_name ]].</p>',

        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Booking Assigned!',
            'mail_subject' => 'Booking Assigned!',
            'whatsapp_subject' => 'Booking Assigned!',
            'sms_subject' => 'Booking Assigned!',
            'template_detail' => '<p>#[[ booking_id ]] - [[ assignee_name ]] has been assigned to [[ booking_services_name ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - [[ assignee_name ]] has been assigned to [[ booking_services_name ]].</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - [[ assignee_name ]] has been assigned to [[ booking_services_name ]].</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - [[ assignee_name ]] has been assigned to [[ booking_services_name ]].</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'transfer_booking',
            'name' => 'transfer_booking',
            'label' => 'Booking Transferred',
            'status' => 1,
            'to' => '["handyman"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);


        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Booking Transferred!',
            'template_detail' => '<p>#[[ booking_id ]] - You have been unassigned from the booking for [[ booking_services_name ]].</p>',
        ]);



        $template = NotificationTemplate::create([
            'type' => 'update_booking_status',
            'name' => 'update_booking_status',
            'label' => 'Booking Update',
            'status' => 1,
            'to' => '["admin", "provider" , "handyman" , "user"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'status' => 1,
            'subject' => 'Booking Update',
            'mail_subject' => 'Booking Update',
            'whatsapp_subject' => 'Booking Update',
            'sms_subject' => 'Booking Update',
            'template_detail' => '<p>#[[ booking_id ]] - Status for [[ booking_services_name ]] has changed to [[ booking_status ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - Status for [[ booking_services_name ]] has changed to [[ booking_status ]].</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - Status for [[ booking_services_name ]] has changed to [[ booking_status ]].</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - Status for [[ booking_services_name ]] has changed to [[ booking_status ]].</p>',

        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Booking Update',
            'mail_subject' => 'Booking Update',
            'whatsapp_subject' => 'Booking Update',
            'sms_subject' => 'Booking Update',
            'template_detail' => '<p>#[[ booking_id ]] - Status for [[ booking_services_name ]] has changed to [[ booking_status ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - Status for [[ booking_services_name ]] has changed to [[ booking_status ]].</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - Status for [[ booking_services_name ]] has changed to [[ booking_status ]].</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - Status for [[ booking_services_name ]] has changed to [[ booking_status ]].</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Booking Update',
            'mail_subject' => 'Booking Update',
            'whatsapp_subject' => 'Booking Update',
            'sms_subject' => 'Booking Update',
            'template_detail' => '<p>#[[ booking_id ]] - Status for [[ booking_services_name ]] has changed to [[ booking_status ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - Status for [[ booking_services_name ]] has changed to [[ booking_status ]].</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - Status for [[ booking_services_name ]] has changed to [[ booking_status ]].</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - Status for [[ booking_services_name ]] has changed to [[ booking_status ]].</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Booking Confirmation',
            'mail_subject' => 'Booking Confirmation',
            'whatsapp_subject' => 'Booking Confirmation',
            'sms_subject' => 'Booking Confirmation',
            'template_detail' => '<p>#[[ booking_id ]] - Status for [[ booking_services_name ]] has changed to [[ booking_status ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - Status for [[ booking_services_name ]] has changed to [[ booking_status ]].</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - Status for [[ booking_services_name ]] has changed to [[ booking_status ]].</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - Status for [[ booking_services_name ]] has changed to [[ booking_status ]].</p>',

        ]);

        $template = NotificationTemplate::create([
            'type' => 'cancel_booking',
            'name' => 'cancel_booking',
            'label' => 'Cancel On Booking',
            'status' => 1,
            'to' => '["admin", "provider" , "handyman" , "user"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Booking Cancelled',
            'mail_subject' => 'Booking Cancelled',
            'whatsapp_subject' => 'Booking Cancelled',
            'sms_subject' => 'Booking Cancelled',
            'template_detail' => '<p>#[[ booking_id ]] - [[ booking_services_name ]] has been cancelled.</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - [[ booking_services_name ]] has been cancelled.</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - [[ booking_services_name ]] has been cancelled.</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - [[ booking_services_name ]] has been cancelled.</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Booking Cancelled',
            'mail_subject' => 'Booking Cancelled',
            'whatsapp_subject' => 'Booking Cancelled',
            'sms_subject' => 'Booking Cancelled',
            'template_detail' => '<p>#[[ booking_id ]] - [[ booking_services_name ]] has been cancelled.</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - [[ booking_services_name ]] has been cancelled.</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - [[ booking_services_name ]] has been cancelled.</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - [[ booking_services_name ]] has been cancelled.</p>',

        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Booking Cancelled',
            'mail_subject' => 'Booking Cancelled',
            'whatsapp_subject' => 'Booking Cancelled',
            'sms_subject' => 'Booking Cancelled',
            'template_detail' => '<p>#[[ booking_id ]] - [[ booking_services_name ]] has been cancelled.</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - [[ booking_services_name ]] has been cancelled.</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - [[ booking_services_name ]] has been cancelled.</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - [[ booking_services_name ]] has been cancelled.</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Booking Cancelled',
            'mail_subject' => 'Booking Cancelled',
            'whatsapp_subject' => 'Booking Cancelled',
            'sms_subject' => 'Booking Cancelled',
            'template_detail' => '<p>#[[ booking_id ]] - [[ booking_services_name ]] has been cancelled.</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - [[ booking_services_name ]] has been cancelled.</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - [[ booking_services_name ]] has been cancelled.</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - [[ booking_services_name ]] has been cancelled.</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'payment_message_status',
            'name' => 'payment_message_status',
            'label' => 'Payment Status Update',
            'status' => 1,
            'to' => '["user","handyman","provider","admin"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Payment Status Update',
            'mail_subject' => 'Payment Status Update',
            'whatsapp_subject' => 'Payment Status Update',
            'sms_subject' => 'Payment Status Update',
            'template_detail' => '<p>#[[ booking_id ]] - Payment For [[ booking_services_name ]] changed to [[ payment_status ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - Payment For [[ booking_services_name ]] changed to [[ payment_status ]].</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - Payment For [[ booking_services_name ]] changed to [[ payment_status ]].</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - Payment For [[ booking_services_name ]] changed to [[ payment_status ]].</p>',

        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Payment Status Update',
            'mail_subject' => 'Payment Status Update',
            'whatsapp_subject' => 'Payment Status Update',
            'sms_subject' => 'Payment Status Update',
            'template_detail' => '<p>#[[ booking_id ]] - Payment For [[ booking_services_name ]] changed to [[ payment_status ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - Payment For [[ booking_services_name ]] changed to [[ payment_status ]].</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - Payment For [[ booking_services_name ]] changed to [[ payment_status ]].</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - Payment For [[ booking_services_name ]] changed to [[ payment_status ]].</p>',

        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Payment Status Update',
            'mail_subject' => 'Payment Status Update',
            'whatsapp_subject' => 'Payment Status Update',
            'sms_subject' => 'Payment Status Update',
            'template_detail' => '<p>#[[ booking_id ]] - Payment For [[ booking_services_name ]] changed to [[ payment_status ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - Payment For [[ booking_services_name ]] changed to [[ payment_status ]].</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - Payment For [[ booking_services_name ]] changed to [[ payment_status ]].</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - Payment For [[ booking_services_name ]] changed to [[ payment_status ]].</p>',

        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Payment Status Update',
            'mail_subject' => 'Payment Status Update',
            'whatsapp_subject' => 'Payment Status Update',
            'sms_subject' => 'Payment Status Update',
            'template_detail' => '<p>#[[ booking_id ]] - Payment For [[ booking_services_name ]] changed to [[ payment_status ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - Payment For [[ booking_services_name ]] changed to [[ payment_status ]].</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - Payment For [[ booking_services_name ]] changed to [[ payment_status ]].</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - Payment For [[ booking_services_name ]] changed to [[ payment_status ]].</p>',

        ]);

        $template = NotificationTemplate::create([
            'type' => 'wallet_payout_transfer',
            'name' => 'wallet_payout_transfer',
            'label' => 'Wallet Payout Transfer',
            'status' => 1,
            'to' => '["admin","provider","handyman"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Wallet Payout Transfer',
            'mail_subject' => 'Wallet Payout Transfer',
            'whatsapp_subject' => 'Wallet Payout Transfer',
            'sms_subject' => 'Wallet Payout Transfer',
            'template_detail' => '<p>Payout of [[ pay_amount ]] has been processed.</p>',
            'whatsapp_template_detail' => '<p>You have received a payout of [[ pay_amount ]].</p>',
            'sms_template_detail' => '<p>You have received a payout of [[ pay_amount ]].</p>',
            'mail_template_detail' => '<p>You have received a payout of [[ pay_amount ]].</p>',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Payout Received',
            'mail_subject' => 'Payout Received',
            'whatsapp_subject' => 'Payout Received',
            'sms_subject' => 'Payout Received',
            'template_detail' => '<p>You have received a payout of [[ pay_amount ]].</p>',
            'whatsapp_template_detail' => '<p>You have received a payout of [[ pay_amount ]].</p>',
            'sms_template_detail' => '<p>You have received a payout of [[ pay_amount ]].</p>',
            'mail_template_detail' => '<p>You have received a payout of [[ pay_amount ]].</p>',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Payout Received',
            'mail_subject' => 'Payout Received',
            'whatsapp_subject' => 'Payout Received',
            'sms_subject' => 'Payout Received',
            'template_detail' => '<p>You have received a payout of [[ pay_amount ]].</p>',
            'whatsapp_template_detail' => '<p>You have received a payout of [[ pay_amount ]].</p>',
            'sms_template_detail' => '<p>You have received a payout of [[ pay_amount ]].</p>',
            'mail_template_detail' => '<p>You have received a payout of [[ pay_amount ]].</p>',

        ]);

        $template = NotificationTemplate::create([
            'type' => 'wallet_top_up',
            'name' => 'wallet_top_up',
            'label' => 'Wallet Top Up',
            'status' => 1,
            'to' => '["admin","provider","user"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Wallet Top-Up',
            'mail_subject' => 'Wallet Top-Up',
            'whatsapp_subject' => 'Wallet Top-Up',
            'sms_subject' => 'Wallet Top-Up',
            'template_detail' => '<p>[[ customer_name ]] has topped up wallet with [[ credit_debit_amount ]].</p>',
            'whatsapp_template_detail' => '<p>[[ customer_name ]] has topped up wallet with [[ credit_debit_amount ]].</p>',
            'sms_template_detail' => '<p>[[ customer_name ]] has topped up wallet with [[ credit_debit_amount ]].</p>',
            'mail_template_detail' => '<p>[[ customer_name ]] has topped up wallet with [[ credit_debit_amount ]].</p>',

        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Wallet Top-Up',
            'mail_subject' => 'Wallet Top-Up',
            'whatsapp_subject' => 'Wallet Top-Up',
            'sms_subject' => 'Wallet Top-Up',
            'template_detail' => '<p>[[ credit_debit_amount ]] has been added to your wallet.</p>',
            'whatsapp_template_detail' => '<p>[[ credit_debit_amount ]] has been added to your wallet.</p>',
            'sms_template_detail' => '<p>[[ credit_debit_amount ]] has been added to your wallet.</p>',
            'mail_template_detail' => '<p>[[ credit_debit_amount ]] has been added to your wallet.</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Wallet Top-Up',
            'mail_subject' => 'Wallet Top-Up',
            'whatsapp_subject' => 'Wallet Top-Up',
            'sms_subject' => 'Wallet Top-Up',
            'template_detail' => '<p>[[ credit_debit_amount ]] has been added to your wallet.</p>',
            'whatsapp_template_detail' => '<p>[[ credit_debit_amount ]] has been added to your wallet.</p>',
            'sms_template_detail' => '<p>[[ credit_debit_amount ]] has been added to your wallet.</p>',
            'mail_template_detail' => '<p>[[ credit_debit_amount ]] has been added to your wallet.</p>',

        ]);

        $template = NotificationTemplate::create([
            'type' => 'wallet_refund',
            'name' => 'wallet_refund',
            'label' => 'Wallet Refund',
            'status' => 1,
            'to' => '["admin","provider","user"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Wallet Refund',
            'mail_subject' => 'Wallet Refund',
            'whatsapp_subject' => 'Wallet Refund',
            'sms_subject' => 'Wallet Refund',
            'template_detail' => '<p>#[[ booking_id ]] - Refund of [[ refund_amount ]] has been processed to the customer.</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - Refund of [[ refund_amount ]] has been processed to the customer.</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - Refund of [[ refund_amount ]] has been processed to the customer.</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - Refund of [[ refund_amount ]] has been processed to the customer.</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Wallet Refund',
            'mail_subject' => 'Wallet Refund',
            'whatsapp_subject' => 'Wallet Refund',
            'sms_subject' => 'Wallet Refund',
            'template_detail' => '<p>#[[ booking_id ]] - Refund of [[ refund_amount ]] has been processed to the customer.</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - Refund of [[ refund_amount ]] has been processed to the customer.</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - Refund of [[ refund_amount ]] has been processed to the customer.</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - Refund of [[ refund_amount ]] has been processed to the customer.</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Wallet Refund',
            'mail_subject' => 'Wallet Refund',
            'whatsapp_subject' => 'Wallet Refund',
            'sms_subject' => 'Wallet Refund',
            'template_detail' => '<p>#[[ booking_id ]] - Refund of [[ refund_amount ]] has been processed to your wallet.</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - Refund of [[ refund_amount ]] has been processed to your wallet.</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - Refund of [[ refund_amount ]] has been processed to your wallet.</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - Refund of [[ refund_amount ]] has been processed to your wallet.</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'paid_with_wallet',
            'name' => 'paid_with_wallet',
            'label' => 'Paid For Booking',
            'status' => 1,
            'to' => '["admin","provider","handyman","user"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Payment Paid For Booking',
            'mail_subject' => 'Payment Paid For Booking',
            'whatsapp_subject' => 'Payment Paid For Booking',
            'sms_subject' => 'Payment Paid For Booking',
            'template_detail' => '<p>#[[ booking_id ]] - [[ customer_name ]] has paid payment of [[ amount ]] using wallet.</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - [[ customer_name ]] has paid payment of [[ amount ]] using wallet.</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - [[ customer_name ]] has paid payment of [[ amount ]] using wallet.</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - [[ customer_name ]] has paid payment of [[ amount ]] using wallet.</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Payment Paid For Booking',
            'mail_subject' => 'Payment Paid For Booking',
            'whatsapp_subject' => 'Payment Paid For Booking',
            'sms_subject' => 'Payment Paid For Booking',
            'template_detail' => '<p>#[[ booking_id ]] - [[ customer_name ]] has paid payment of [[ amount ]] using wallet.</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - [[ customer_name ]] has paid payment of [[ amount ]] using wallet.</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - [[ customer_name ]] has paid payment of [[ amount ]] using wallet.</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - [[ customer_name ]] has paid payment of [[ amount ]] using wallet.</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Payment Paid For Booking',
            'mail_subject' => 'Payment Paid For Booking',
            'whatsapp_subject' => 'Payment Paid For Booking',
            'sms_subject' => 'Payment Paid For Booking',
            'template_detail' => '<p>#[[ booking_id ]] - [[ customer_name ]] has paid payment of [[ amount ]] using wallet.</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - [[ customer_name ]] has paid payment of [[ amount ]] using wallet.</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - [[ customer_name ]] has paid payment of [[ amount ]] using wallet.</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - [[ customer_name ]] has paid payment of [[ amount ]] using wallet.</p>',

        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Payment Paid For Booking',
            'mail_subject' => 'Payment Paid For Booking',
            'whatsapp_subject' => 'Payment Paid For Booking',
            'sms_subject' => 'Payment Paid For Booking',
            'template_detail' => '<p>#[[ booking_id ]] - Payment of [[ amount ]] using wallet paid successfully.</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - Payment of [[ amount ]] using wallet paid successfully.</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - Payment of [[ amount ]] using wallet paid successfully.</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - Payment of [[ amount ]] using wallet paid successfully.</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'job_requested',
            'name' => 'job_requested',
            'label' => 'New Custom Job Request',
            'status' => 1,
            'to' => '["admin","provider"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'New Custom Job Request',
            'status' => 1,
            'user_type' => 'admin',
            'subject' => 'New Custom Job Request',
            'mail_subject' => 'New Custom Job Request',
            'whatsapp_subject' => 'New Custom Job Request',
            'sms_subject' => 'New Custom Job Request',
            'template_detail' => '<p>#[[ job_id ]] - [[ customer_name ]] has requested a new job [[ job_name ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ job_id ]] - [[ customer_name ]] has requested a new job [[ job_name ]].</p>',
            'sms_template_detail' => '<p>#[[ job_id ]] - [[ customer_name ]] has requested a new job [[ job_name ]].</p>',
            'mail_template_detail' => '<p>#[[ job_id ]] - [[ customer_name ]] has requested a new job [[ job_name ]].</p>',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => 'New Custom Job Request',
            'status' => 1,
            'user_type' => 'provider',
            'subject' => 'New Custom Job Request',
            'mail_subject' => 'New Custom Job Request',
            'whatsapp_subject' => 'New Custom Job Request',
            'sms_subject' => 'New Custom Job Request',
            'template_detail' => '<p>#[[ job_id ]] - [[ customer_name ]] has requested a new job [[ job_name ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ job_id ]] - [[ customer_name ]] has requested a new job [[ job_name ]].</p>',
            'sms_template_detail' => '<p>#[[ job_id ]] - [[ customer_name ]] has requested a new job [[ job_name ]].</p>',
            'mail_template_detail' => '<p>#[[ job_id ]] - [[ customer_name ]] has requested a new job [[ job_name ]].</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'provider_send_bid',
            'name' => 'provider_send_bid',
            'label' => 'Provider Send Bid',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'New Bid Received',
            'mail_subject' => 'New Bid Received',
            'whatsapp_subject' => 'New Bid Received',
            'sms_subject' => 'New Bid Received',
            'template_detail' => '<p>#[[ job_id ]] - You have received a new bid of [[ bid_amount ]] from [[ provider_name ]] on your job request. Check it out!</p>',
            'whatsapp_template_detail' => '<p>#[[ job_id ]] - You have received a new bid of [[ bid_amount ]] from [[ provider_name ]] on your job request. Check it out!</p>',
            'sms_template_detail' => '<p>#[[ job_id ]] - You have received a new bid of [[ bid_amount ]] from [[ provider_name ]] on your job request. Check it out!</p>',
            'mail_template_detail' => '<p>#[[ job_id ]] - You have received a new bid of [[ bid_amount ]] from [[ provider_name ]] on your job request. Check it out!</p>',

        ]);

        $template = NotificationTemplate::create([
            'type' => 'user_accept_bid',
            'name' => 'user_accept_bid',
            'label' => 'Bid Accepted',
            'status' => 1,
            'to' => '["provider"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Job Bid Accepted',
            'mail_subject' => 'Job Bid Accepted',
            'whatsapp_subject' => 'Job Bid Accepted',
            'sms_subject' => 'Job Bid Accepted',
            'template_detail' => '<p>#[[ job_request_id ]] - Your bid of [[ job_price ]] for the job request has been accepted by [[ customer_name ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ job_request_id ]] - Your bid of [[ job_price ]] for the job request has been accepted by [[ customer_name ]].</p>',
            'sms_template_detail' => '<p>#[[ job_request_id ]] - Your bid of [[ job_price ]] for the job request has been accepted by [[ customer_name ]].</p>',
            'mail_template_detail' => '<p>#[[ job_request_id ]] - Your bid of [[ job_price ]] for the job request has been accepted by [[ customer_name ]].</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'provider_payout',
            'name' => 'provider_payout',
            'label' => 'Provider Payout',
            'status' => 1,
            'to' => '["provider","admin"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Payout Received',
            'mail_subject' => 'Payout Received',
            'whatsapp_subject' => 'Payout Received',
            'sms_subject' => 'Payout Received',
            'template_detail' => '<p>Your payout of [[ amount ]] has been received.</p>',
            'whatsapp_template_detail' => '<p>Your payout of [[ amount ]] has been received.</p>',
            'sms_template_detail' => '<p>Your payout of [[ amount ]] has been received.</p>',
            'mail_template_detail' => '<p>Your payout of [[ amount ]] has been received.</p>',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Payout Processed',
            'mail_subject' => 'Payout Processed',
            'whatsapp_subject' => 'Payout Processed',
            'sms_subject' => 'Payout Processed',
            'template_detail' => '<p>Payout of [[ amount ]] has been processed to [[ provider_name ]].</p>',
            'whatsapp_template_detail' => '<p>Payout of [[ amount ]] has been processed to [[ provider_name ]].</p>',
            'sms_template_detail' => '<p>Payout of [[ amount ]] has been processed to [[ provider_name ]].</p>',
            'mail_template_detail' => '<p>Payout of [[ amount ]] has been processed to [[ provider_name ]].</p>',
        ]);



        $template = NotificationTemplate::create([
            'type' => 'subscription_add',
            'name' => 'subscription_add',
            'label' => 'Subscription Add',
            'status' => 1,
            'to' => '["admin","provider"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'New Subscription Plan Activated',
            'mail_subject' => 'New Subscription Plan Activated',
            'whatsapp_subject' => 'New Subscription Plan Activated',
            'sms_subject' => 'New Subscription Plan Activated',
            'template_detail' => '<p>[[ provider_name ]] has subscribed to a new [[ plan_name ]].</p>',
            'whatsapp_template_detail' => '<p>[[ provider_name ]] has subscribed to a new [[ plan_name ]].</p>',
            'sms_template_detail' => '<p>[[ provider_name ]] has subscribed to a new [[ plan_name ]].</p>',
            'mail_template_detail' => '<p>[[ provider_name ]] has subscribed to a new [[ plan_name ]].</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'New Subscription Plan Activated',
            'mail_subject' => 'New Subscription Plan Activated',
            'whatsapp_subject' => 'New Subscription Plan Activated',
            'sms_subject' => 'New Subscription Plan Activated',
            'template_detail' => '<p>New plan susbcribed - [[ plan_name ]].</p>',
            'whatsapp_template_detail' => '<p>New plan susbcribed - [[ plan_name ]].</p>',
            'sms_template_detail' => '<p>New plan susbcribed - [[ plan_name ]].</p>',
            'mail_template_detail' => '<p>New plan susbcribed - [[ plan_name ]].</p>',
        ]);
        // create
        $template = NotificationTemplate::create([
            'type' => 'service_request',
            'name' => 'service_request',
            'label' => 'Service Request ',
            'status' => 1,
            'to' => '["admin"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Create New Service Request ',
            'template_detail' => '<p>Create New Service Request.</p>',
        ]);
        // Approved
        $template = NotificationTemplate::create([
            'type' => 'service_request_approved',
            'name' => 'service_request_approved',
            'label' => 'Service Request Approved',
            'status' => 1,
            'to' => '["admin","provider"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Your Service Has Been Approved',
            'template_detail' => '<p>New service request has been approved.</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Your Service Has Been Approved',
            'template_detail' => '<p>Your service request has been approved.</p>',
        ]);

        // reject
        $template = NotificationTemplate::create([
            'type' => 'service_request_reject',
            'name' => 'service_request_reject',
            'label' => 'Service Request Reject',
            'status' => 1,
            'to' => '["provider"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Your Service Request Was Rejected',
            'template_detail' => '<p>Unfortunately, your service request was rejected.</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'register',
            'name' => 'register',
            'label' => 'Register',
            'status' => 1,
            'to' => '["admin","provider","handyman","user"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'New User Registration',
            'mail_subject' => 'New User Registration',
            'whatsapp_subject' => 'New User Registration',
            'sms_subject' => 'New User Registration',
            'template_detail' => '<p>[[ user_name ]] is now registered with us!</p>',
            'whatsapp_template_detail' => '<p>[[ user_name ]] is now registered with us!</p>',
            'sms_template_detail' => '<p>[[ user_name ]] is now registered with us!</p>',
            'mail_template_detail' => '<p>[[ user_name ]] is now registered with us!</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'New User Registration',
            'mail_subject' => 'New User Registration',
            'whatsapp_subject' => 'New User Registration',
            'sms_subject' => 'New User Registration',
            'template_detail' => '<p>Welcome aboard! We are happy to have you on our team. Your expertise is going to be invaluable to our customers.</p>',
            'whatsapp_template_detail' => '<p>Welcome aboard! We are happy to have you on our team. Your expertise is going to be invaluable to our customers.</p>',
            'sms_template_detail' => '<p>Welcome aboard! We are happy to have you on our team. Your expertise is going to be invaluable to our customers.</p>',
            'mail_template_detail' => '<p>Welcome aboard! We are happy to have you on our team. Your expertise is going to be invaluable to our customers.</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'New User Registration',
            'mail_subject' => 'New User Registration',
            'whatsapp_subject' => 'New User Registration',
            'sms_subject' => 'New User Registration',
            'template_detail' => '<p>Welcome to the team! We are excited to have you on board. Your skills are going to be a huge asset to our customers.</p>',
            'whatsapp_template_detail' => '<p>Welcome to the team! We are excited to have you on board. Your skills are going to be a huge asset to our customers.</p>',
            'sms_template_detail' => '<p>Welcome to the team! We are excited to have you on board. Your skills are going to be a huge asset to our customers.</p>',
            'mail_template_detail' => '<p>Welcome to the team! We are excited to have you on board. Your skills are going to be a huge asset to our customers.</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'New User Registration',
            'mail_subject' => 'New User Registration',
            'whatsapp_subject' => 'New User Registration',
            'sms_subject' => 'New User Registration',
            'template_detail' => '<p>Welcome! We are excited to have you join our community. We are looking forward to seeing how you will use our platform to achieve your goals.</p>',
            'whatsapp_template_detail' => '<p>Welcome! We are excited to have you join our community. We are looking forward to seeing how you will use our platform to achieve your goals.</p>',
            'sms_template_detail' => '<p>Welcome! We are excited to have you join our community. We are looking forward to seeing how you will use our platform to achieve your goals.</p>',
            'mail_template_detail' => '<p>Welcome! We are excited to have you join our community. We are looking forward to seeing how you will use our platform to achieve your goals.</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'withdraw_money',
            'name' => 'withdraw_money',
            'label' => 'Withdraw Money',
            'status' => 1,
            'to' => '["admin","provider","user"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Money Withdrawn',
            'mail_subject' => 'Money Withdrawn',
            'whatsapp_subject' => 'Money Withdrawn',
            'sms_subject' => 'Money Withdrawn',
            'template_detail' => '<p>[[ user_name ]] has withdrawn [[ amount ]] from the wallet.</p>',
            'whatsapp_template_detail' => '<p>[[ user_name ]] has withdrawn [[ amount ]] from the wallet.</p>',
            'sms_template_detail' => '<p>[[ user_name ]] has withdrawn [[ amount ]] from the wallet.</p>',
            'mail_template_detail' => '<p>[[ user_name ]] has withdrawn [[ amount ]] from the wallet.</p>',

        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Money Withdrawn',
            'mail_subject' => 'Money Withdrawn',
            'whatsapp_subject' => 'Money Withdrawn',
            'sms_subject' => 'Money Withdrawn',
            'template_detail' => '<p>You have withdrawn [[ amount ]] from the wallet.</p>',
            'whatsapp_template_detail' => '<p>You have withdrawn [[ amount ]] from the wallet.</p>',
            'sms_template_detail' => '<p>You have withdrawn [[ amount ]] from the wallet.</p>',
            'mail_template_detail' => '<p>You have withdrawn [[ amount ]] from the wallet.</p>',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Money Withdrawn',
            'mail_subject' => 'Money Withdrawn',
            'whatsapp_subject' => 'Money Withdrawn',
            'sms_subject' => 'Money Withdrawn',
            'template_detail' => '<p>You have withdrawn [[ amount ]] from the wallet.</p>',
            'whatsapp_template_detail' => '<p>You have withdrawn [[ amount ]] from the wallet.</p>',
            'sms_template_detail' => '<p>You have withdrawn [[ amount ]] from the wallet.</p>',
            'mail_template_detail' => '<p>You have withdrawn [[ amount ]] from the wallet.</p>',

        ]);
        $template = NotificationTemplate::create([
            'type' => 'handyman_payout',
            'name' => 'handyman_payout',
            'label' => 'Handyman Payout',
            'status' => 1,
            'to' => '["provider","handyman"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Payout Received',
            'mail_subject' => 'Payout Received',
            'whatsapp_subject' => 'Payout Received',
            'sms_subject' => 'Payout Received',
            'template_detail' => '<p>Your payout of [[ amount ]] has been received from [[ provider_name ]].</p>',
            'whatsapp_template_detail' => '<p>Your payout of [[ amount ]] has been received from [[ provider_name ]].</p>',
            'sms_template_detail' => '<p>Your payout of [[ amount ]] has been received from [[ provider_name ]].</p>',
            'mail_template_detail' => '<p>Your payout of [[ amount ]] has been received from [[ provider_name ]].</p>',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Payout Processed',
            'mail_subject' => 'Payout Processed',
            'whatsapp_subject' => 'Payout Processed',
            'sms_subject' => 'Payout Processed',
            'template_detail' => '<p>Payout of [[ amount ]] has been processed to [[ handyman_name ]].</p>',
            'whatsapp_template_detail' => '<p>Payout of [[ amount ]] has been processed to [[ handyman_name ]].</p>',
            'sms_template_detail' => '<p>Payout of [[ amount ]] has been processed to [[ handyman_name ]].</p>',
            'mail_template_detail' => '<p>Payout of [[ amount ]] has been processed to [[ handyman_name ]].</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'add_helpdesk',
            'name' => 'add_helpdesk',
            'label' => 'Query confirmation',
            'status' => 1,
            'to' => '["admin"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'New Query Received',
            'mail_subject' => 'New Query Received',
            'whatsapp_subject' => 'New Query Received',
            'sms_subject' => 'New Query Received',
            'template_detail' => '<p>New Query [[ sender_name ]] - [[ subject ]].</p>',
            'whatsapp_template_detail' => '<p>New Query [[ sender_name ]] - [[ subject ]].</p>',
            'sms_template_detail' => '<p>New Query [[ sender_name ]] - [[ subject ]].</p>',
            'mail_template_detail' => '<p>New Query [[ sender_name ]] - [[ subject ]].</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'closed_helpdesk',
            'name' => 'closed_helpdesk',
            'label' => 'Closed',
            'status' => 1,
            'to' => '["admin","provider","handyman","user"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Query Closed',
            'mail_subject' => 'Query Closed',
            'whatsapp_subject' => 'Query Closed',
            'sms_subject' => 'Query Closed',
            'template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'sms_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'mail_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Query Closed',
            'mail_subject' => 'Query Closed',
            'whatsapp_subject' => 'Query Closed',
            'sms_subject' => 'Query Closed',
            'template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'sms_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'mail_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Query Closed',
            'mail_subject' => 'Query Closed',
            'whatsapp_subject' => 'Query Closed',
            'sms_subject' => 'Query Closed',
            'template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'sms_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'mail_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Query Closed',
            'mail_subject' => 'Query Closed',
            'whatsapp_subject' => 'Query Closed',
            'sms_subject' => 'Query Closed',
            'template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'sms_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
            'mail_template_detail' => '<p>#[[ helpdesk_id ]] closed by [[ sender_name ]].</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'reply_helpdesk',
            'name' => 'reply_helpdesk',
            'label' => 'Replied Query',
            'status' => 1,
            'to' => '["admin","provider","handyman","user"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Query Replied',
            'mail_subject' => 'Query Replied',
            'whatsapp_subject' => 'Query Replied',
            'sms_subject' => 'Query Replied',
            'template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'sms_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'mail_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Query Replied',
            'mail_subject' => 'Query Replied',
            'whatsapp_subject' => 'Query Replied',
            'sms_subject' => 'Query Replied',
            'template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'sms_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'mail_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Query Replied',
            'mail_subject' => 'Query Replied',
            'whatsapp_subject' => 'Query Replied',
            'sms_subject' => 'Query Replied',
            'template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'sms_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'mail_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Query Replied',
            'mail_subject' => 'Query Replied',
            'whatsapp_subject' => 'Query Replied',
            'sms_subject' => 'Query Replied',
            'template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'whatsapp_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'sms_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
            'mail_template_detail' => '<p>#[[ helpdesk_id ]] replied by [[ sender_name ]].</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'cancellation_charges',
            'name' => 'cancellation_charges',
            'label' => 'Cancellation Charges',
            'status' => 1,
            'to' => '["admin","user"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '0', 'IS_SMS' => '1', 'IS_WHATSAPP' => '1'],
        ]);
        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Cancellation Charges',
            'mail_subject' => 'Cancellation Charges',
            'whatsapp_subject' => 'Cancellation Charges',
            'sms_subject' => 'Cancellation Charges',
            'template_detail' => `<p>#[[ booking_id ]] - A cancellation charge of [[ paid_amount ]] has been deducted from the customer's wallet.</p>`,
            'whatsapp_template_detail' => `<p>#[[ booking_id ]] - A cancellation charge of [[ paid_amount ]] has been deducted from the customer's wallet.</p>`,
            'sms_template_detail' => `<p>#[[ booking_id ]] - A cancellation charge of [[ paid_amount ]] has been deducted from the customer's wallet.</p>`,
            'mail_template_detail' => `<p>#[[ booking_id ]] - A cancellation charge of [[ paid_amount ]] has been deducted from the customer's wallet.</p>`,
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Cancellation Charges',
            'mail_subject' => 'Cancellation Charges',
            'whatsapp_subject' => 'Cancellation Charges',
            'sms_subject' => 'Cancellation Charges',
            'template_detail' => '<p>#[[ booking_id ]] - A cancellation charge [[ paid_amount ]] has been deducted from your wallet.</p>',
            'whatsapp_template_detail' => '<p>#[[ booking_id ]] - A cancellation charge [[ paid_amount ]] has been deducted from your wallet.</p>',
            'sms_template_detail' => '<p>#[[ booking_id ]] - A cancellation charge [[ paid_amount ]] has been deducted from your wallet.</p>',
            'mail_template_detail' => '<p>#[[ booking_id ]] - A cancellation charge [[ paid_amount ]] has been deducted from your wallet.</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'promotional_banner',
            'name' => 'promotional_banner',
            'label' => 'Promotional Banner Created',
            'status' => 1,
            'to' => '["admin","provider"]',
            'channels' => ['IS_MAIL' => '0', 'PUSH_NOTIFICATION' => '1', 'IS_SMS' => '0', 'IS_WHATSAPP' => '0'],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'New Promotional Banner Created',
            'mail_subject' => 'New Promotional Banner Created',
            'whatsapp_subject' => 'New Promotional Banner Created',
            'sms_subject' => 'New Promotional Banner Created',
            'template_detail' => '<p>A new promotional banner has been created by [[ provider_name ]].</p>',
            'whatsapp_template_detail' => '<p>A new promotional banner has been created by [[ provider_name ]].</p>',
            'sms_template_detail' => '<p>A new promotional banner has been created by [[ provider_name ]].</p>',
            'mail_template_detail' => '<p>A new promotional banner has been created by [[ provider_name ]].</p>',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Promotional Banner Submitted',
            'mail_subject' => 'Promotional Banner Submitted',
            'whatsapp_subject' => 'Promotional Banner Submitted',
            'sms_subject' => 'Promotional Banner Submitted',
            'template_detail' => '<p>Your promotional banner has been successfully submitted and is pending approval.</p>',
            'whatsapp_template_detail' => '<p>Your promotional banner has been successfully submitted and is pending approval.</p>',
            'sms_template_detail' => '<p>Your promotional banner has been successfully submitted and is pending approval.</p>',
            'mail_template_detail' => '<p>Your promotional banner has been successfully submitted and is pending approval.</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'promotional_banner_accepted',
            'name' => 'promotional_banner_accepted',
            'label' => 'Promotional Banner Accepted',
            'status' => 1,
            'to' => '["provider"]',
            'channels' => [
                'IS_MAIL' => '0',
                'PUSH_NOTIFICATION' => '1',
                'IS_SMS' => '0',
                'IS_WHATSAPP' => '0'
            ],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Promotional Banner is Accepted',
            'mail_subject' => 'Promotional Banner is Accepted',
            'whatsapp_subject' => 'Promotional Banner is Accepted',
            'sms_subject' => 'Promotional Banner is Accepted',
            'template_detail' => '<p>Your promotional banner has been accepted and is now live.</p>',
            'whatsapp_template_detail' => '<p>Your promotional banner has been accepted and is now live.</p>',
            'sms_template_detail' => '<p>Your promotional banner has been accepted and is now live.</p>',
            'mail_template_detail' => '<p>Your promotional banner has been accepted and is now live.</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'promotional_banner_rejected',
            'name' => 'promotional_banner_rejected',
            'label' => 'Promotional Banner Rejected',
            'status' => 1,
            'to' => '["provider"]',
            'channels' => [
                'IS_MAIL' => '0',
                'PUSH_NOTIFICATION' => '1',
                'IS_SMS' => '0',
                'IS_WHATSAPP' => '0'
            ],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Promotional Banner is Rejected',
            'mail_subject' => 'Promotional Banner is Rejected',
            'whatsapp_subject' => 'Promotional Banner is Rejected',
            'sms_subject' => 'Promotional Banner is Rejected',
            'template_detail' => '<p>Your promotional banner has been rejected. Reason: [[ reject_reason ]].</p>',
            'whatsapp_template_detail' => '<p>Your promotional banner has been rejected. Reason: [[ reject_reason ]].</p>',
            'sms_template_detail' => '<p>Your promotional banner has been rejected. Reason: [[ reject_reason ]].</p>',
            'mail_template_detail' => '<p>Your promotional banner has been rejected. Reason: [[ reject_reason ]].</p>',
        ]);

        $template = NotificationTemplate::create([
            'type' => 'wallet_refund_promotional_banner',
            'name' => 'wallet_refund_promotional_banner',
            'label' => 'Wallet Refund Initiated for Promotional Banner',
            'status' => 1,
            'to' => '["admin", "provider"]',
            'channels' => [
                'IS_MAIL' => '0',
                'PUSH_NOTIFICATION' => '1',
                'IS_SMS' => '0',
                'IS_WHATSAPP' => '0'
            ],
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Wallet Refund Initiated for Rejected Banner',
            'mail_subject' => 'Wallet Refund Initiated for Rejected Banner',
            'whatsapp_subject' => 'Wallet Refund Initiated for Rejected Banner',
            'sms_subject' => 'Wallet Refund Initiated for Rejected Banner',
            'template_detail' => '<p>A refund of [[ refund_amount ]] has been initiated for the rejected promotional banner.</p>',
            'whatsapp_template_detail' => '<p>A refund of [[ refund_amount ]] has been initiated for the rejected promotional banner.</p>',
            'sms_template_detail' => '<p>A refund of [[ refund_amount ]] has been initiated for the rejected promotional banner.</p>',
            'mail_template_detail' => '<p>A refund of [[ refund_amount ]] has been initiated for the rejected promotional banner.</p>',
        ]);

        $template->defaultNotificationTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Refund Processed for Rejected Banner',
            'mail_subject' => 'Refund Processed for Rejected Banner',
            'whatsapp_subject' => 'Refund Processed for Rejected Banner',
            'sms_subject' => 'Refund Processed for Rejected Banner',
            'template_detail' => '<p>Your wallet has been credited with [[ refund_amount ]] due to the rejection of your promotional banner titled [[ banner_title ]].</p>',
            'whatsapp_template_detail' => '<p>Your wallet has been credited with [[ refund_amount ]] due to the rejection of your promotional banner titled [[ banner_title ]].</p>',
            'sms_template_detail' => '<p>Your wallet has been credited with [[ refund_amount ]] due to the rejection of your promotional banner titled [[ banner_title ]].</p>',
            'mail_template_detail' => '<p>Your wallet has been credited with [[ refund_amount ]] due to the rejection of your promotional banner titled [[ banner_title ]].</p>',
        ]);


    }
}
