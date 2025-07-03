<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class NotificationStatePayTrue extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public string $pdfPath;

    public function __construct(string $pdfPath)
    {
        $this->pdfPath = $pdfPath;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Comprobante de pago',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.post-payTrue',
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromPath($this->pdfPath)
                ->as('Comprobante de Pago.pdf')
                ->withMime('application/pdf'),
        ];
    }
}