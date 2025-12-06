<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationStatusUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public $application;
    public $body;
    public $subjectLine;

    /**
     * Create a new message instance.
     */
    public function __construct($application)
    {
        $this->application = $application;
        
        $settings = \App\Models\LazSetting::all()->pluck('value', 'key');
        
        $this->subjectLine = $settings['email_status_update_subject'] ?? 'Update Status Permohonan - {code}';
        $this->body = $settings['email_status_update_body'] ?? "Halo {applicant_name},\n\nStatus permohonan bantuan Anda ({code}) telah diperbarui menjadi: {status}.\n\nSilakan cek detailnya di portal kami.\n\nSalam,\nTim LAZ";

        // Replace placeholders
        $placeholders = [
            '{code}' => $application->code,
            '{applicant_name}' => $application->applicant_name,
            '{status}' => $application->status,
            '{program_name}' => $application->program->name ?? '-',
        ];

        foreach ($placeholders as $key => $value) {
            $this->subjectLine = str_replace($key, $value, $this->subjectLine);
            $this->body = str_replace($key, $value, $this->body);
        }
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $settings = \App\Models\LazSetting::all()->pluck('value', 'key');
        $fromAddress = $settings['email_sender_address'] ?? config('mail.from.address');
        $fromName = $settings['email_sender_name'] ?? config('mail.from.name');

        return new Envelope(
            from: new \Illuminate\Mail\Mailables\Address($fromAddress, $fromName),
            subject: $this->subjectLine,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.generic',
            with: [
                'body' => $this->body,
                'subject' => $this->subjectLine,
            ],
        );
    }

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
