<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Queue\SerializesModels;

class NewMessageMail extends Mailable
{
    use Queueable, SerializesModels;

    public $sender, $subjectLine, $bodyText, $attachments, $post;

    public function __construct(array $data)
    {
        $this->sender      = $data['sender'];
        $this->subjectLine = $data['subject'];
        $this->bodyText        = $data['bodyText'];
        $this->attachments = $data['attachments'] ?? [];
        $this->post        = $data['post'];
    }

    /**
     * Message envelope (subject, from, replyTo).
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subjectLine,
            from: new Address(
                config('mail.from.address'),
                config('mail.from.name')
            ),
            replyTo: [
                new Address($this->sender->email, $this->sender->name),
            ],
        );
    }

    /**
     * The content of the email (view + data).
     */
    public function content(): Content
    {
        return new Content(
            view: 'messages.new_message',
            with: [
                'sender'      => $this->sender,         
                'subjectLine' => $this->subjectLine,   
                'bodyText'        => $this->bodyText,          
                'post'        => $this->post,          
            ],
        );
    }


    /**
     * Attach any files the user uploaded.
     */
    public function attachments(): array
    {
        return collect($this->attachments)
            ->map(fn(string $file) =>
                Attachment::fromPath(
                    storage_path("app/public/messages/{$file}")
                )
            )->all();
    }
}
