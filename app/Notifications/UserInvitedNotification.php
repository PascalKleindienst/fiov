<?php

declare(strict_types=1);

namespace App\Notifications;

use App\Enums\UserLevel;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\URL;

final class UserInvitedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public readonly UserLevel $userLevel, public readonly User $sender) {}

    /**
     * @return string[]
     */
    public function via(AnonymousNotifiable $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(AnonymousNotifiable $notifiable): MailMessage
    {
        $appName = config('app.name');

        $url = $this->generateInvitationUrl($notifiable->routes['mail']);

        return (new MailMessage)
            ->subject(__('users.invite.subject'))
            ->greeting(__('users.invite.greeting'))
            ->line(__('users.invite.body', ['name' => $this->sender->name, 'app' => $appName]))
            ->action(__('users.invite.action'), url($url))
            ->line(__('users.invite.note'));
    }

    public function generateInvitationUrl(string $email): string
    {
        return URL::temporarySignedRoute('register', now()->addDay(), [
            'level' => $this->userLevel,
            'email' => $email,
        ]);
    }

    /**
     * @return array{user_level: UserLevel, sender: User, email: string, url: string}
     */
    public function toArray(AnonymousNotifiable $notifiable): array
    {
        return [
            'user_level' => $this->userLevel,
            'sender' => $this->sender,
            'email' => $notifiable->routes['mail'],
            'url' => $this->generateInvitationUrl($notifiable->routes['mail']),
        ];
    }
}
