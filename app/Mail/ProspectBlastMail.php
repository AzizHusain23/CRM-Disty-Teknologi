<?php

namespace App\Mail;

use App\Models\Campaign;
use App\Models\Lead;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProspectBlastMail extends Mailable
{
    use Queueable, SerializesModels;

    public $campaign;
    public $lead;
    /**
     * Create a new message instance.
     */
    public function __construct(Campaign $campaign, Lead $lead)
    {
        $this->campaign = $campaign;
        $this->lead = $lead;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->campaign->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.prospect',
            with: [
                'nama' => $this->lead->nama,
                'institusi' => $this->lead->institusi,
                'body' => $this->campaign->body,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
