<?php
namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;
use App\Models\Message;

class MitraSentMessageNotification extends Notification
{
    use Queueable;

    protected Message $message;

    public function __construct(Message $message)
    {
        $this->message = $message;
    }

    public function via($notifiable)
    {
        return ['database','mail'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'message_id' => $this->message->id,
            'post_id'    => $this->message->post_id,
            'subject'    => $this->message->subject,
            'body'       => $this->message->body,
            'sent_at'    => now(),
        ];
    }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("Pesan baru dari Mitra")
            ->greeting("Halo {$notifiable->name},")
            ->line("Anda mendapat pesan baru untuk “{$this->message->post->title}”.")
            ->action('Lihat Pesan', route('posts.messages.index', $this->message->post))
            ->salutation('Terima kasih');
    }
}
