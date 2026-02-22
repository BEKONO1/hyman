<?php

namespace App\Notifications;

use App\Broadcasting\CustomWebhook;
// use App\Broadcasting\OneSingleChannel;
use App\Mail\MailMailableSend;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\NotificationTemplate;
use App\Broadcasting\FcmChannel;
use App\Models\MailTemplateContentMapping;
use App\Models\MailTemplates;
use App\Models\NotificationTemplateContentMapping;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use App\Models\Setting;
use App\Models\User;
use App\Traits\NotificationTrait;


class CommonNotification extends Notification implements ShouldQueue
{
    use Queueable, NotificationTrait;

    public $type;

    public $data;

    public $subject;

    public $notification;

    public $notification_message;

    public $notification_link;

    public $appData;

    public $custom_webhook;

    public $template_data;

    /**
     * Create a new notification instance.
     */
    public function __construct($type, $data)
    {
        $this->type = $type;
        $this->data = $data;

        $userType = $data['user_type'];
        $notifications = NotificationTemplate::where('type', $this->type)
            ->with('defaultNotificationTemplateMap')
            ->first();
        $notify_data = NotificationTemplateContentMapping::where('template_id', $notifications->id)->get();
        $templateData = $notify_data->where('user_type', $userType)->first();
        $this->template_data = $templateData;
        $templateDetail = $templateData->template_detail ?? null;
        foreach ($this->data as $key => $value) {
            $templateDetail = str_replace('[[ ' . $key . ' ]]', $this->data[$key], $templateDetail);
        }
        $this->data['type'] = $templateData->subject ?? 'None';
        $this->data['message'] = $templateDetail ?? __('messages.default_notification_body');
        $this->appData = $notifications->channels;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        $notificationData = $this->data;
        $templateData = $this->template_data;

        $notificationSettings = $this->appData;
        $notification_settings = [];
        $notification_access = isset($notificationSettings[$this->type]) ? $notificationSettings[$this->type] : [];
        if (isset($notificationSettings)) {
            foreach ($notificationSettings as $key => $notification) {
                if ($notification) {

                    switch ($key) {

                        case 'PUSH_NOTIFICATION':

                            Log::info($notification_settings);
                            array_push($notification_settings, FcmChannel::class);

                            break;

                        case 'IS_CUSTOM_WEBHOOK':
                            array_push($notification_settings, CustomWebhook::class);

                            break;

                        case 'IS_MAIL':

                            Log::info($notification_settings);
                            array_push($notification_settings, 'mail');

                            break;
                        case 'IS_SMS':
                            $templateDetail = $templateData->sms_template_detail ?? null;
                            foreach ($notificationData as $key => $value) {
                                $templateDetail = str_replace('[[ ' . $key . ' ]]', $notificationData[$key], $templateDetail);
                            }

                            $notificationData['type'] = $templateData->sms_subject ?? 'None';
                            $notificationData['message'] = $templateDetail ?? __('messages.default_notification_body');

                            $this->sendSmsMessage($notificationData);

                            break;
                        case 'IS_WHATSAPP':
                            $templateDetail = $templateData->whatsapp_template_detail ?? null;
                            foreach ($notificationData as $key => $value) {
                                $templateDetail = str_replace('[[ ' . $key . ' ]]', $notificationData[$key], $templateDetail);
                            }

                            $notificationData['type'] = $templateData->whatsapp_subject ?? 'None';
                            $notificationData['message'] = $templateDetail ?? __('messages.default_notification_body');

                            $this->sendWhatsAppMessage($notificationData);
                            break;
                    }
                }
            }
        }
        return array_merge($notification_settings, ['database']);
    }



    /**
     * Get mail notification
     *
     * @param  mixed  $notifiable
     * @return MailMailableSend
     */
  public function toMail($notifiable)
{
    $userType = strtolower($this->data['user_type']);
    $email = $notifiable->email ?? ($notifiable->routes['mail'] ?? null);

    if (!$email) {
        \Log::error("No email found for notifiable.");
        return null;
    }

    // Step 1: Get the main mail template (just for type matching)
    $mail = MailTemplates::where('type', $this->type)->first();
    if (!$mail) {
        \Log::error("Mail template not found for type: {$this->type}");
        return null;
    }

    // Step 2: Get subject from mail_template_content_mappings
    $templateData = MailTemplateContentMapping::where('template_id', $mail->id)
        ->where('user_type', $userType)
        ->first();

    if (!$templateData) {
        \Log::error("Subject not found in mail_template_content_mappings for template_id: {$mail->id}, user_type: {$userType}");
        return null;
    }

    $subject = $templateData->subject;

    // Step 3: Use subject + user_type to find mail body from notification_template_content_mapping
    $customTemplate = NotificationTemplateContentMapping::where('subject', $subject)
        ->where('user_type', $userType)
        ->first();

    $mailBody = $customTemplate->mail_template_detail ?? '**Body not found for subject & user_type**';

    // Optional logging
    \Log::info("Mail subject: {$subject}, user_type: {$userType}");
    \Log::info("Mail body fetched:", ['body' => $mailBody]);

    // Assign data
    $this->subject = $subject;
    $this->data['type'] = $subject;
    $this->data['message'] = $mailBody;

    return (new MailMailableSend($this->notification, $this->data, $this->type))
        ->to($email)
        ->bcc(json_decode($this->notification->bcc ?? '[]'))
        ->cc(json_decode($this->notification->cc ?? '[]'))
        ->subject($this->subject);
}



    public function toFcm($notifiable)
    {
        $msg = strip_tags($this->data['message']);
        if (!isset($msg) && $msg == '') {
            $msg = __('message.notification_body');
        }
        $type = 'booking';
        if (isset($this->data['type']) && $this->data['type'] !== '') {
            $type = $this->data['type'];
        }

        $heading = $this->data['type'] ?? '';

        $additionalData = json_encode($this->data);
        return fcm([
            "message" => [
                "topic" => 'user_' . $notifiable->id,
                "notification" => [
                    "title" => $heading,
                    "body" => $msg,
                ],
                "data" => [
                    "sound" => "default",
                    "title" => $heading,
                    "body" => $msg,
                    "story_id" => "story_12345",
                    "type" => $type,
                    "additional_data" => $additionalData,
                    "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                ],
                "android" => [
                    "priority" => "high",
                    "notification" => [
                        "click_action" => "FLUTTER_NOTIFICATION_CLICK",
                    ],
                ],
                "apns" => [
                    "payload" => [
                        "aps" => [
                            "category" => "NEW_MESSAGE_CATEGORY",
                        ],
                    ],
                ],
            ],
        ]);
    }



    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return $this->data;
    }

    // public function sendNotification($data)
    // {
    //     return $this->data;
    // }

    function sendSmsMessage($data)
    {
        $settingData = Setting::where('type', 'OTHER_SETTING')->first();
        $settings = json_decode($settingData->value, true);

        $user = User::where('id', $data['person_id'])->first();

        if (empty($settings['twilio_sid_sms']) || empty($settings['twilio_auth_token_sms']) || empty($settings['twilio_phone_number_sms'])) {
            return false;
        }

        if (empty($user) || empty($data['message'])) {
            return false;
        }

        $sid = $settings['twilio_sid_sms'];
        $authToken = $settings['twilio_auth_token_sms'];
        $twilioPhoneNumber = $settings['twilio_phone_number_sms'];
        $recipientNumber = '+' . $user->contact_number;
        $messageBody = strip_tags($data['message']);

        $client = new Client($sid, $authToken);

        try {
            $message = $client->messages->create(
                $recipientNumber,
                [
                    'from' => $twilioPhoneNumber,
                    'body' => $messageBody
                ]
            );

            return true;

        } catch (\Exception $e) {
            // dd($e->getMessage());
            return false;
        }
    }

    function sendWhatsAppMessage($data)
    {
        $settingData = Setting::where('type', 'OTHER_SETTING')->first();
        $settings = json_decode($settingData->value, true);

        $user = User::where('id', $data['person_id'])->first();


        if (empty($settings['twilio_sid_whatsapp']) || empty($settings['twilio_auth_token_whatsapp']) || empty($settings['twilio_whatsapp_number'])) {
            return false;
        }

        if (empty($user) || empty($user->contact_number) || empty($data['message'])) {
            return false;
        }

        $sid = $settings['twilio_sid_whatsapp'];
        $authToken = $settings['twilio_auth_token_whatsapp'];
        $twilioWhatsAppNumber = 'whatsapp:' . $settings['twilio_whatsapp_number'];
        $recipientNumber = 'whatsapp:' . $user->contact_number;
        $messageBody = strip_tags($data['message']);
        $client = new Client($sid, $authToken);

        try {
            $client->messages->create(
                $recipientNumber,
                [
                    'from' => $twilioWhatsAppNumber,
                    'body' => $messageBody
                ]
            );
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Send notification using the NotificationTrait.
     *
     * @param mixed $data
     * @param int $personId
     * @return void
     */

}
