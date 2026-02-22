<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class MailMailableSend extends Mailable
{
    use Queueable, SerializesModels;

    public $mailable;

    public $data;

    public $templateData;

    public $type;

    /**
     * Create a new message instance.
     */
    public function __construct($mailable, $data, $type = '')
    {
        $this->mailable = $mailable ?? '';
        $this->data = $data;
        $this->type = $type;

    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->templateData = $this->data['message'] != '' ? $this->data['message'] : __('messages.default_notification_body');
        foreach ($this->data as $key => $value) {
            $this->templateData = str_replace('[[ '.$key.' ]]', $this->data[$key], $this->templateData);

        }

        $message = $this->markdown('mail.markdown');

        $files = isset($this->data['attachments']) ? json_decode($this->data['attachments']) : [];

        foreach ($files as $file) {
            $message->attach($file); // attach each file
        }

        return $message; //Send mail
    }
}
