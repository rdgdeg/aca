<?php

namespace App\Mail;

use App\Models\Submission;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SubmissionAckMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Submission $submission) {}

    public function envelope(): Envelope
    {
        $locale = $this->submission->locale ?: 'fr';
        $subject = $this->submission->form->getTranslation('ack_subject', $locale, false)
            ?: $this->submission->form->getTranslation('ack_subject', 'fr', false)
            ?: 'Nous avons bien reçu votre message';

        return new Envelope(subject: $subject);
    }

    public function content(): Content
    {
        return new Content(markdown: 'mail.submission-ack');
    }
}
