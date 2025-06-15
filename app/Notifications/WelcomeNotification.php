<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * TODO: add actual messages
 * TODO: add NotificationDTO for DB notifications
 * TODO: add tests
 */
final class WelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct() {}

    /**
     * @return string[]
     */
    public function via(): array
    {
        return ['mail', 'database'];
    }

    public function toMail(User $notifiable): MailMessage
    {
        return (new MailMessage())
            ->line('Welcome to our application, '.$notifiable->name.'!');
    }

    /**
     * @return array{title: string, message: string}
     */
    public function toArray(User $notifiable): array
    {
        return [
            'title' => 'Welcome '.$notifiable->name,
            'message' => 'Welcome to our application!',
        ];
    }
}
