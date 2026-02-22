<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ServiceStatusUpdated extends Mailable
{
    use Queueable, SerializesModels;
    public $status;
    public $serviceId;
     /**
     * Create a new message instance.
     *
     * @param string $status
     * @param int $serviceId
     */
    public function __construct($status, $serviceId)
    {
        $this->status = $status;
        $this->serviceId = $serviceId;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Service Status Updated',
        );
    }

    public function build()
    {
        return $this->from('admin@admin.com') // Set the "From" address here
                    ->view('emails.statusUpdated') // Create an email view
                    ->with([
                        'status' => $this->status,
                        'serviceId' => $this->serviceId,
                    ]);
    }
    
    /**
     * Get the message content definition.
     */
   

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
