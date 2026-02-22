<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Constant;
use App\Models\MailTemplates;

class MailTemplateSeeder extends Seeder
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
         * MailTemplatess Seed
         * ------------------
         */

        // DB::table('MailTemplatess')->truncate();
        // echo "Truncate: MailTemplatess \n";

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
                'value' => 'transfer_booking',
                'name' => 'Transfer Booking',
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
            // [
            //     'type' => 'notification_type',
            //     'value' => 'add_wallet',
            //     'name' => 'Add Wallet',
            // ],
            // [
            //     'type' => 'notification_type',
            //     'value' => 'update_wallet',
            //     'name' => 'Update Wallet',
            // ],
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
                'name' => 'Service Request ',
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
                'value' => 'service_name',
                'name' => 'Service Name',
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
                'value' => 'user_email',
                'name' => 'User Email',
            ],
            [
                'type' => 'notification_param_button',
                'value' => 'banner_title',
                'name' => 'Banner Title',
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
        ];

        foreach ($types as $value) {
            Constant::updateOrCreate(['type' => $value['type'], 'value' => $value['value']], $value);
        }

        // echo " Insert: mailtempletes \n\n";

        // Enable foreign key checks!
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        DB::table('mail_templates')->delete();
        DB::table('mail_template_content_mappings')->delete();

        $template = MailTemplates::create([
            'type' => 'add_booking',
            'name' => 'add_booking',
            'label' => 'New Booking',
            'status' => 1,
            'to' => '["admin","provider"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'New Booking Received',
            'template_detail' => '<p>Hello [[ admin_name ]],</p>
                                  <p>Below are the booking details for a recent booking request received from a customer.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Booking Details:</strong></p>
                                  <ul>
                                  <li>Customer Name: [[ customer_name ]]</li>
                                  <li>Booking ID: #[[ booking_id ]]</li>
                                  <li>Service Requested: [[ booking_services_name]]</li>
                                  <li>Date: [[ booking_date ]]</li>
                                  <li>Time: [[ booking_time ]]</li>
                                  <li>Location: [[ venue_address ]]</li>
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
            'subject' => 'New Booking Received',
            'template_detail' => '<p>Hello [[ provider_name ]],</p>
                                  <p>Below are the booking details for a recent booking request received from a [[ customer_name ]].</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Booking Details:</strong></p>
                                  <ul>
                                  <li>Booking ID: #[[ booking_id ]]</li>
                                  <li>Service Requested: [[ booking_services_name ]]</li>
                                  <li>Date: [[ booking_date ]]</li>
                                  <li>Time: [[ booking_time ]]</li>
                                  <li>Location: [[ venue_address ]]</li>
                                  </ul>
                                  <p>&nbsp;</p>
                                  <p>Best regards, <br />[[ company_name ]]</p>',
        ]);

        $template = MailTemplates::create([
            'type' => 'assigned_booking',
            'name' => 'assigned_booking',
            'label' => 'Booking Assigned',
            'status' => 1,
            'to' => '["handyman","user","provider"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Booking Assigned!',
            'template_detail' => '<p>Hello [[ handyman_name ]],</p>
                                  <p>You have been assigned to manage a booking. Please be prepared to provide service for [[ booking_services_name ]].</p>
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
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Booking Assigned!',
            'template_detail' => '<p>Hello [[ customer_name ]],</p>
                                  <p>This message is to inform you that your booking #[[ booking_id ]] has been assigned to [[ assignee_name ]].</p>
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
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Booking Assigned!',
            'template_detail' => '<p>Hello [[ provider_name ]],</p>
                                  <p>You have been assigned to handle a booking #[[ booking_id ]]. Please be prepared to provide service for [[ booking_services_name ]].</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Booking</strong><strong>&nbsp;Details:</strong></p>
                                  <ul>
                                  <li>Service Requested: [[ booking_services_name ]]</li>
                                  <li>Date: [[ booking_date ]]</li>
                                  <li>Time: [[ booking_time ]]</li>
                                  <li>Location: [[ venue_address ]]</li>
                                  </ul>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);

        $template = MailTemplates::create([
            'type' => 'transfer_booking',
            'name' => 'transfer_booking',
            'label' => 'Booking Transferred',
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
            'subject' => 'Booking Unassigned!',
            'template_detail' => '<p>Hello [[ handyman_name ]],</p>
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





        $template = MailTemplates::create([
            'type' => 'update_booking_status',
            'name' => 'update_booking_status',
            'label' => 'Update Booking',
            'status' => 1,
            'to' => '["admin", "provider" , "handyman" , "user"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Booking Update',
            'template_detail' => '<p>Hello [[ admin_name ]],</p>
                                  <p>This is to notify you that the status of a booking #[[ booking_id ]] for [[ booking_services_name ]] has changed to [[ booking_status ]].</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Booking Update',
            'template_detail' => '<p>Hello [[ provider_name ]],</p>
                                  <p>This is to notify you that the status of a booking #[[ booking_id ]] for [[ booking_services_name ]] has changed to [[ booking_status ]].</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Booking Update',
            'template_detail' => '<p>Hello [[ handyman_name ]],</p>
                                  <p>This is to notify you that the status of a booking #[[ booking_id ]] for [[ booking_services_name ]] has changed to [[ booking_status ]].</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Booking Confirmation',
            'template_detail' => '<p>Hello [[ customer_name ]],</p>
                                  <p>This is to notify you that the status of a booking #[[ booking_id ]] for [[ booking_services_name ]] has changed to [[ booking_status ]].</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);

        $template = MailTemplates::create([
            'type' => 'cancel_booking',
            'name' => 'cancel_booking',
            'label' => 'Cancel On Booking',
            'status' => 1,
            'to' => '["admin", "provider" , "handyman" , "user"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Booking Cancelled',
            'template_detail' => '<p>Hello [[ admin_name ]],</p>
                                  <p>This is to notify you that a booking #[[ booking_id ]] for [[ booking_services_name ]] has been cancelled by [[ cancelled_user_name ]].</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Booking Details:</strong></p>
                                  <ul>
                                  <li>Cancelled Service: [[ booking_services_name ]]</li>
                                  <li>Booking ID: [[ booking_id ]]</li>
                                  <li>Date: [[ booking_date ]]</li>
                                  <li>Time: [[ booking_time ]]</li>
                                  <li>Location: [[ venue_address ]]</li>
                                  </ul>
                                  <p>Thank you for your attention to this matter.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Booking Cancelled',
            'template_detail' => '<p>Hello [[ provider_name ]],</p>
                                  <p>This is to notify you that a booking #[[ booking_id ]] for [[ booking_services_name ]] has been cancelled by [[ cancelled_user_name ]]. Please review the details below and take any necessary actions.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Booking Details:</strong></p>
                                  <ul>
                                  <li>Cancelled Service: [[ booking_services_name ]]</li>
                                  <li>Booking ID: [[ booking_id ]]</li>
                                  <li>Date: [[ booking_date ]]</li>
                                  <li>Time: [[ booking_time ]]</li>
                                  <li>Location: [[ venue_address ]]</li>
                                  </ul>
                                  <p>Thank you for your attention to this matter.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Booking Cancelled',
            'template_detail' => '<p>Hello [[ handyman_name ]],</p>
                                  <p>This is to notify you that a booking #[[ booking_id ]] for [[ booking_services_name ]] has been cancelled by [[ cancelled_user_name ]]. Please review the details below and take any necessary actions.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Booking Details:</strong></p>
                                  <ul>
                                  <li>Cancelled Service: [[ booking_services_names ]]</li>
                                  <li>Booking ID: [[ booking_id ]]</li>
                                  <li>Date: [[ booking_date ]]</li>
                                  <li>Time: [[ booking_time ]]</li>
                                  <li>Location: [[ venue_address ]]</li>
                                  </ul>
                                  <p>Thank you for your attention to this matter.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Booking Cancelled',
            'template_detail' => '<p>Hello [[ customer_name ]],</p>
                                  <p>This is to notify you that a booking #[[ booking_id ]] for [[ booking_services_name ]] has been cancelled by [[ cancelled_user_name ]]. Please review the details below and take any necessary actions.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Booking Details:</strong></p>
                                  <ul>
                                  <li>Cancelled Service: [[ booking_services_names ]]</li>
                                  <li>Booking ID: [[ booking_id ]]</li>
                                  <li>Date: [[ booking_date ]]</li>
                                  <li>Time: [[ booking_time ]]</li>
                                  <li>Location: [[ venue_address ]]</li>
                                  </ul>
                                  <p>Thank you for your attention to this matter.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);

        $template = MailTemplates::create([
            'type' => 'payment_message_status',
            'name' => 'payment_message_status',
            'label' => 'Payment Message Status',
            'status' => 1,
            'to' => '["user","handyman","provider","admin"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Payment Status Update',
            'template_detail' => '<p>Hello [[ customer_name ]],</p>
                                  <p>#[[ booking_id ]] - Your booking payment status has been changed to [[ payment_status ]]. Please check your booking details for more information.</p>
                                  <p>Should you have any inquiries or require further assistance regarding this payment status change, please do not hesitate to contact our dedicated support team at [[ company_contact_info ]].</p>
                                  <p>Thank you for choosing our services.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);

        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Payment Status Update',
            'template_detail' => '<p>Hello [[ admin_name ]],</p>
                                  <p>#[[ booking_id ]] - Payment status has been changed to [[ payment_status ]].</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',

        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Payment Status Update',
            'template_detail' => '<p>Hello [[ provider_name ]],</p>
                                  <p>#[[ booking_id ]] - Payment status has been changed to [[ payment_status ]]. Please check booking details for more information.</p>
                                  <p>Should you have any inquiries or require further assistance regarding this payment status change, please do not hesitate to contact our dedicated support team at [[ company_contact_info ]].</p>
                                  <p>Thank you for choosing our services.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);

        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Payment Status Update',
            'template_detail' => '<p>Hello [[ handyman_name ]],</p>
                                  <p>#[[ booking_id ]] - Payment status has been changed to [[ payment_status ]]. Please check booking details for more information.</p>
                                  <p>Should you have any inquiries or require further assistance regarding this payment status change, please do not hesitate to contact our dedicated support team at [[ company_contact_info ]].</p>
                                  <p>Thank you for choosing our services.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);


        $template = MailTemplates::create([
            'type' => 'wallet_payout_transfer',
            'name' => 'wallet_payout_transfer',
            'label' => 'Wallet Payout Transfer',
            'status' => 1,
            'to' => '["admin","provider","handyman"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Wallet Payout',
            'template_detail' => '<p>Hello [[ admin_name ]],</p>
                                  <p>Payout transfer of [[ pay_amount ]] has been successfully processed to [[ user_name ]].</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);

        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Payout Received',
            'template_detail' => '<p>Hello [[ provider_name]],</p>
                                  <p>We are pleased to inform you that a payout transfer of [[ pay_amount ]] has been successfully processed.</p>
                                  <p>If you have any questions or need further assistance, please do not hesitate to contact us.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);

        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Payout Received',
            'template_detail' => '<p>Hello [[ handyman_name]],</p>
                                 <p>We are pleased to inform you that a payout transfer of [[ pay_amount ]] has been successfully processed.</p>
                                 <p>If you have any questions or need further assistance, please do not hesitate to contact us.</p>
                                 <p>&nbsp;</p>
                                 <p>Best regards,<br />[[ company_name ]]</p>',
        ]);

        $template = MailTemplates::create([
            'type' => 'wallet_top_up',
            'name' => 'wallet_top_up',
            'label' => 'Wallet Top Up',
            'status' => 1,
            'to' => '["admin","provider","user"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Wallet Top-Up',
            'template_detail' => '<p>Hello [[ admin_name ]],</p>
                                  <p>[[ customer_name ]] topped up wallet with [[ credit_debit_amount ]] successfully.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Transaction Details:</strong></p>
                                  <ul>
                                  <li>Transaction ID: [[ wallet_transaction_id ]]</li>
                                  <li>Transaction Type: [[ wallet_transaction_type ]]</li>
                                  <li>Amount: [[ wallet_amount ]]</li>
                                  </ul>
                                  <p>If you have any questions or need further assistance, please do not hesitate to contact us.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Wallet Top-Up',
            'template_detail' => '<p>Hello [[ provider_name ]],</p>
                                  <p>We are pleased to inform you that [[ credit_debit_amount ]] has been added to your wallet.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Transaction Details:</strong></p>
                                  <ul>
                                  <li>Transaction ID: [[ wallet_transaction_id ]]</li>
                                  <li>Transaction Type: [[ wallet_transaction_type ]]</li>
                                  <li>Amount: [[ wallet_amount ]]</li>
                                  </ul>
                                  <p>If you have any questions or need further assistance, please do not hesitate to contact us.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Wallet Top-Up',
            'template_detail' => '<p>Hello [[ customer_name ]],</p>
                                  <p>Your wallet has been topped up with [[ credit_debit_amount ]] successfully.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Transaction Details:</strong></p>
                                  <ul>
                                  <li>Transaction ID: [[ wallet_transaction_id ]]</li>
                                  <li>Transaction Type: [[ wallet_transaction_type ]]</li>
                                  <li>Amount: [[ wallet_amount ]]</li>
                                  </ul>
                                  <p>If you have any questions or need further assistance, please do not hesitate to contact us.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);

        $template = MailTemplates::create([
            'type' => 'wallet_refund',
            'name' => 'wallet_refund',
            'label' => 'Wallet Refund',
            'status' => 1,
            'to' => '["admin","provider","user"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Wallet Refund',
            'template_detail' => '<p>Hello [[ admin_name ]],</p>
                                  <p>We would like to inform you that the service provided by [[ provider_name ]] to [[ customer_name ]] has been cancelled. Consequently, a refund of [[ refund_amount ]] has been processed to the customer.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Wallet Refund',
            'template_detail' => '<p>Hello [[ provider_name ]],</p>
                                  <p>We would like to inform you that the service provided by you to [[ customer_name ]] has been cancelled. Consequently, a refund of [[ refund_amount ]] has been processed to the customer.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Wallet Refund',
            'template_detail' => '<p>Hello [[ customer_name ]],</p>
                                  <p>We would like to inform you that the service provided by [[ provider_name ]] to you has been cancelled. Consequently, a refund of [[ refund_amount ]] has been processed to you wallet.</p>
                                  <p>If you have any further questions or concerns, please dont hesitate to contact us.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);

        $template = MailTemplates::create([
            'type' => 'paid_with_wallet',
            'name' => 'paid_with_wallet',
            'label' => 'Paid For Booking',
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
            'subject' => 'Payment Paid For Booking',
            'template_detail' => '<p>Hello [[ admin_name ]],</p>
                                  <p>#[[ booking_id ]] - Payment of [[ amount ]] using wallet paid successfully. Please review the details below.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Booking Details:</strong></p>
                                  <ul>
                                  <li>Booking ID: [[ booking_id ]]</li>
                                  <li>Service: [[ booking_services_name ]]</li>
                                  <li>Customer: [[ customer_name ]]</li>
                                  <li>Date: [[ booking_date ]]</li>
                                  <li>Time: [[ booking_time ]]</li>
                                  <li>Amount: [[ amount ]]</li>
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
            'subject' => 'Payment Paid For Booking',
            'template_detail' => '<p>Hello [[ provider_name ]],</p>
                                  <p>#[[ booking_id ]] - Payment of [[ amount ]] using wallet paid successfully. Please review the details below and manage the booking accordingly.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Booking Details:</strong></p>
                                  <ul>
                                  <li>Booking ID: [[ booking_id ]]</li>
                                  <li>Service: [[ booking_services_name ]]</li>
                                  <li>Customer: [[ customer_name ]]</li>
                                  <li>Date: [[ booking_date ]]</li>
                                  <li>Time: [[ booking_time ]]</li>
                                  <li>Amount: [[ amount ]]</li>
                                  </ul>
                                  <p>Thank you for choosing our services. We look forward to serving you.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Payment Paid For Booking',
            'template_detail' => '<p>Hello [[ handyman_name ]],</p>
                                  <p>#[[ booking_id ]] - Payment of [[ amount ]] using wallet paid successfully. Please review the details below and manage the booking accordingly.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Booking Details:</strong></p>
                                  <ul>
                                  <li>Booking ID: [[ booking_id ]]</li>
                                  <li>Service: [[ booking_services_name ]]</li>
                                  <li>Customer: [[ customer_name ]]</li>
                                  <li>Date: [[ booking_date ]]</li>
                                  <li>Time: [[ booking_time ]]</li>
                                  <li>Amount: [[ amount ]]</li>
                                  </ul>
                                  <p>Thank you for choosing our services. We look forward to serving you.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Payment Paid For Booking',
            'template_detail' => '<p>Hello [[ customer_name ]],</p>
                                  <p>#[[ booking_id ]] - Payment of [[ amount ]] using wallet paid successfully.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Booking Details:</strong></p>
                                  <ul>
                                  <li>Booking ID: [[ booking_id ]]</li>
                                  <li>Service: [[ booking_services_name ]]</li>
                                  <li>Date: [[ booking_date ]]</li>
                                  <li>Time: [[ booking_time ]]</li>
                                  <li>Amount: [[ amount ]]</li>
                                  </ul>
                                  <p>Thank you for choosing our services. We look forward to serving you.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);

        $template = MailTemplates::create([
            'type' => 'job_requested',
            'name' => 'job_requested',
            'label' => 'New Post Job Request',
            'status' => 1,
            'to' => '["admin","provider"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'New Custom Job Request',
            'template_detail' => '<p>Hello [[ admin_name ]],</p>
                                  <p>#[[ job_id ]] - [[ customer_name ]] has requested a new job request [[ job_name ]].</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'New Custom Job Request',
                'template_detail' => '<p>Hello [[ provider_name ]],</p>
                                      <p>#[[ job_id ]] - [[ customer_name ]] has requested a new job request [[ job_name ]].</p>
                                      <p>&nbsp;</p>
                                      <p>Best regards,<br />[[ company_name ]]</p>',
        ]);

        $template = MailTemplates::create([
            'type' => 'provider_send_bid',
            'name' => 'provider_send_bid',
            'label' => 'Provider Send Bid',
            'status' => 1,
            'to' => '["user"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'New Bid Received',
            'template_detail' => '<p>Hello [[ customer_name ]],</p>
                                  <p>A provider has placed a bid in response to your job request #[[ job_id ]].</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Here are the details:</strong></p>
                                  <ul>
                                  <li>Job Description<strong>:</strong> [[ job_description ]]</li>
                                  <li>Bid Amount: [[ bid_amount ]]</li>
                                  <li>Provider: [[ provider_name ]]</li>
                                  </ul>
                                  <p>Feel free to review the bid and proceed accordingly.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);

        $template = MailTemplates::create([
            'type' => 'user_accept_bid',
            'name' => 'user_accept_bid',
            'label' => 'User Accept Bid',
            'status' => 1,
            'to' => '["provider"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Bid Accepted',
            'template_detail' => '<p>Hello [[ provider_name ]],</p>
                                  <p>Your bid of [[ job_price ]] for the job #[[ job_request_id ]] - [[ job_name ]] request has been accepted by the [[ customer_name ]].</p>
                                  <p>Please proceed with the necessary steps to fulfill the job requirements.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);

        $template = MailTemplates::create([
            'type' => 'provider_payout',
            'name' => 'provider_payout',
            'label' => 'Payout Update',
            'status' => 1,
            'to' => '["provider","admin"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Payout Received',
            'template_detail' => '<p>Hello [[ provider_name ]],</p>
                                  <p>Your payout of [[ amount ]] has been received.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);

        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Payout Processed',
            'template_detail' => '<p>Hello [[ admin_name ]],</p>
                                  <p>Payout of [[ amount ]] has been processed to [[ provider_name ]].</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);





        $template = MailTemplates::create([
            'type' => 'subscription_add',
            'name' => 'subscription_add',
            'label' => 'Subscription Add',
            'status' => 1,
            'to' => '["admin","provider"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'New Subscription Plan Activated',
            'template_detail' => '<p>Hello [[ admin_name ]],</p>
                                  <p>[[ provider_name ]] has susbcribed to a new [[ plan_name ]].</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Subscription Details:</strong></p>
                                  <ul>
                                  <li>Subscription Plan: [[ plan_name ]]</li>
                                  <li>Start Date: [[ start_date ]]</li>
                                  <li>End Date: [[ end_date ]]</li>
                                  <li>Amount: [[ amount ]]</li>
                                  </ul>
                                  <p>Please review the details and take any necessary actions.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'New Subscription Plan Activated',
            'template_detail' => '<p>Hello [[ provider_name ]],</p>
                                  <p>We are excited to inform you that a new subscription has been added to your account.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>Subscription Details:</strong></p>
                                  <ul>
                                  <li>Subscription Plan: [[ plan_name ]]</li>
                                  <li>Start Date: [[ start_date ]]</li>
                                  <li>End Date: [[ end_date ]]</li>
                                  <li>Amount: [[ amount ]]</li>
                                  </ul>
                                  <p>Thank you for choosing our services.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        // create
        $template = MailTemplates::create([
            'type' => 'service_request',
            'name' => 'service_request',
            'label' => 'Service Request',
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
            'subject' => 'Create New Service Request ',
            'template_detail' => '<p>Hello [[ admin_name ]],</p>
                                  <p>We are pleased to inform you Create New Service Request .</p>
                                  <p>&nbsp;</p>
                                  <p><strong>User Details:</strong></p>
                                  <ul>
                                  <li>Name: [[ service_name ]]</li>
                                  </ul>
                                  <p> .</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        // Approved
        $template = MailTemplates::create([
            'type' => 'service_request_approved',
            'name' => 'service_request_approved',
            'label' => 'Service Request  Approved',
            'status' => 1,
            'to' => '["admin","provider"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Your Service Has Been Approved',
            'template_detail' => '<p>Hello [[ admin_name ]],</p>
                                  <p>We are pleased to inform you that a new  service request has been approved..</p>
                                  <p>&nbsp;</p>
                                  <p><strong>User Details:</strong></p>
                                  <ul>
                                  <li>Name: [[ user_name ]]</li>

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
            'subject' => 'Your Service Has Been Approved',
            'template_detail' => '<p>Hello [[ provider_name ]],</p>
                                  <p>Thank you for Your service request has been approved.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        // reject
        $template = MailTemplates::create([
            'type' => 'service_request_reject',
            'name' => 'service_request_reject',
            'label' => 'Service Request Reject',
            'status' => 1,
            'to' => '["provider"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Your Service Request Was Rejected',
            'template_detail' => '<p>Hello [[ provider_name ]],</p>
                                  <p>Unfortunately, your service request was rejected.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);


        $template = MailTemplates::create([
            'type' => 'register',
            'name' => 'register',
            'label' => 'Register',
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
            'subject' => 'New User Registration',
            'template_detail' => '<p>Hello [[ admin_name ]],</p>
                                  <p>We are pleased to inform you that a new user has registered.</p>
                                  <p>&nbsp;</p>
                                  <p><strong>User Details:</strong></p>
                                  <ul>
                                  <li>Name: [[ user_name ]]</li>
                                  <li>Email: [[ user_email ]]</li>
                                  </ul>
                                  <p>Please take any necessary actions to ensure a smooth onboarding experience for our new member.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'New User Registration',
            'template_detail' => '<p>Hello [[ provider_name ]],</p>
                                  <p>Thank you for registering with Handyman Services. We have received your registration request and will review your information shortly. You will receive an email with further instructions once your registration is approved.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);

        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'New User Registration',
            'template_detail' => '<p>Hello [[ handyman_name ]],&nbsp;</p>
                                  <p>Thank you for registering with Handyman Services.&nbsp;</p>
                                  <p>We have received your registration request and will review your information shortly. You will receive an email with further instructions once your registration is approved.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'New User Registration',
            'template_detail' => '<p>Hello [[ user_name ]],</p>
                                  <p>Welcome aboard! We are excited to have you join us at [[ company_name ]]. You are now part of our community, where youll discover a world of opportunities. If you have any questions or need assistance, do not hesitate to reach out. Were here to help!</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);


        $template = MailTemplates::create([
           'type' => 'withdraw_money',
            'name' => 'withdraw_money',
            'label' => 'Withdraw Money',
            'status' => 1,
            'to' => '["admin","provider","user"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'admin',
            'status' => 1,
            'subject' => 'Money Withdrawn',
            'template_detail' => '<p>Hello [[ admin_name ]],</p>
                                  <p>[[ user_name ]] has withdrawn [[ amount ]] from the wallet.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Money Withdrawn',
            'template_detail' => '<p>Hello [[ provider_name ]],</p>
                                  <p>You have withdrawn [[ amount ]] from the wallet.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);

        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'user',
            'status' => 1,
            'subject' => 'Money Withdrawn',
            'template_detail' => '<p>Hello [[ user_name ]],</p>
                                  <p>You have withdrawn [[ amount ]] from the wallet.</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);
        $template = MailTemplates::create([
            'type' => 'handyman_payout',
            'name' => 'handyman_payout',
            'label' => 'Payout Update',
            'status' => 1,
            'to' => '["provider","handyman"]',
            'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
        ]);
        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'handyman',
            'status' => 1,
            'subject' => 'Payout Received',
            'template_detail' => '<p>Hello [[ handyman_name ]],</p>
                                  <p>Your payout of [[ amount ]] has been received from [[ provider_name ]].</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);

        $template->defaultMailTemplateMap()->create([
            'language' => 'en',
            'notification_link' => '',
            'notification_message' => '',
            'user_type' => 'provider',
            'status' => 1,
            'subject' => 'Payout Processed',
            'template_detail' => '<p>Hello [[ provider_name ]],</p>
                                  <p>Payout of [[ amount ]] has been processed to [[ handyman_name ]].</p>
                                  <p>&nbsp;</p>
                                  <p>Best regards,<br />[[ company_name ]]</p>',
        ]);


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
        $template = MailTemplates::create([
            'type' => 'promotional_banner',
             'name' => 'promotional_banner',
             'label' => 'Promotional Banner Created',
             'status' => 1,
             'to' => '["admin","provider"]',
             'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
         ]);
         $template->defaultMailTemplateMap()->create([
            'language' => 'en',
             'notification_link' => '',
             'notification_message' => '',
             'user_type' => 'admin',
             'status' => 1,
             'subject' => 'New Promotional Banner Created',
             'template_detail' => '<p>A new promotional banner  has been created.</p>',
         ]);

         $template->defaultMailTemplateMap()->create([
          'language' => 'en',
             'notification_link' => '',
             'notification_message' => '',
             'user_type' => 'provider',
             'status' => 1,
             'subject' => 'Promotional Banner Submitted',
             'template_detail' => '<p>Your promotional banner has been successfully submitted and is pending approval.</p>',
         ]);

         $template = MailTemplates::create([
            'type' => 'promotional_banner_accepted',
                'name' => 'promotional_banner_accepted',
                'label' => 'Promotional Banner Accepted',
                'status' => 1,
                'to' => '["provider"]',
                'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
            ]);
            $template->defaultMailTemplateMap()->create([
              'language' => 'en',
                'notification_link' => '',
                'notification_message' => '',
                'user_type' => 'provider',
                'status' => 1,
                'subject' => 'Promotional Banner is Accepted',
                'template_detail' => '<p>Your promotional banner has been accepted and is now live.</p>',
            ]);

            $template = MailTemplates::create([
                'type' => 'promotional_banner_rejected',
                     'name' => 'promotional_banner_rejected',
                     'label' => 'Promotional Banner Rejected',
                     'status' => 1,
                     'to' => '["provider"]',
                     'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
                 ]);
                 $template->defaultMailTemplateMap()->create([
                   'language' => 'en',
                     'notification_link' => '',
                     'notification_message' => '',
                     'user_type' => 'provider',
                     'status' => 1,
                     'subject' => 'Promotional Banner is Rejected',
                     'template_detail' => '<p>Your promotional banner has been rejected. Reason: [[ reject_reason ]].</p>',
                 ]);

                 $template = MailTemplates::create([
                    'type' => 'wallet_refund_promotional_banner',
                     'name' => 'wallet_refund_promotional_banner',
                     'label' => 'Wallet Refund Initiated for Promotional Banner',
                     'status' => 1,
                     'to' => '["admin", "provider"]',
                      'channels' => ['IS_MAIL' => '1', 'PUSH_NOTIFICATION' => '1'],
                  ]);
                  $template->defaultMailTemplateMap()->create([
                     'language' => 'en',
                     'notification_link' => '',
                     'notification_message' => '',
                     'user_type' => 'admin',
                     'status' => 1,
                     'subject' => 'Wallet Refund Initiated for Rejected Banner',
                     'template_detail' => '<p>A refund of [[ refund_amount ]] has been initiated for the rejected banner.</p>',
                 ]);

                  $template->defaultMailTemplateMap()->create([
                  'language' => 'en',
                     'notification_link' => '',
                     'notification_message' => '',
                     'user_type' => 'provider',
                     'status' => 1,
                     'subject' => 'Refund Processed for Rejected Banner',
                     'template_detail' => '<p>Your wallet has been credited with [[ refund_amount ]] due to the rejection of your promotional banner .</p>',
                 ]);
    }
}
