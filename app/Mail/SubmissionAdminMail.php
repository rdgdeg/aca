<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionAdminMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Submission $submission) {}

    public function envelope(): Envelope
    {
        $reply = $this->submission->senderEmail();

        return new Envelope(
            subject: '['.$this->submission->form->name.'] Nouveau message',
            replyTo: $reply ? [new Address($reply)] : [],
        );
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.submission-admin');
    }
}
