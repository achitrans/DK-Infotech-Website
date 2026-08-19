<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class InternshipOfferMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $name;
    public string $position;
    public ?string $attachmentPath;

    /**
     * Create a new message instance.
     */
    public function __construct(string $name, string $position = 'Web/Software Development Intern', ?string $attachmentPath = null)
    {
        $this->name = $name;
        $this->position = $position;
        $this->attachmentPath = $attachmentPath;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Internship Offer – ' . config('app.name', 'DK Infotech Solutions'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.internship_offer',
            with: [
                'name' => $this->name,
                'position' => $this->position,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        if ($this->attachmentPath && file_exists($this->attachmentPath)) {
            return [
                Attachment::fromPath($this->attachmentPath)
                    // ->as('Internship_Offer_Letter.pdf')
                    ->as('Internship_Confirmation_Letter.pdf')
                    ->withMime('application/pdf'),
            ];
        }
        return [];
    }
}
