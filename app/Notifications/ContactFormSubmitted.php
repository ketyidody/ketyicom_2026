<?php

namespace App\Notifications;

use App\Models\ContactMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ContactFormSubmitted extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(public ContactMessage $contactMessage)
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Contact Form Submission' . ($this->contactMessage->subject ? ': ' . $this->contactMessage->subject : ''))
            ->line('You have received a new contact form submission.')
            ->line('**From:** ' . $this->contactMessage->name)
            ->line('**Email:** ' . $this->contactMessage->email)
            ->when($this->contactMessage->subject, function ($mail) {
                return $mail->line('**Subject:** ' . $this->contactMessage->subject);
            })
            ->line('**Message:**')
            ->line($this->contactMessage->message)
            ->action('View in Admin', url('/dashboard'))
            ->line('Reply to this message by responding to: ' . $this->contactMessage->email);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'contact_message_id' => $this->contactMessage->id,
            'name' => $this->contactMessage->name,
            'email' => $this->contactMessage->email,
            'subject' => $this->contactMessage->subject,
        ];
    }
}
