<?php

declare(strict_types=1);

namespace App\Livewire\Users;

use App\Attributes\TranslatedFormFields;
use App\Concerns\WithTranslatedFields;
use App\Enums\UserLevel;
use App\Models\User;
use App\Notifications\UserInvitedNotification;
use Flux\Flux;
use Illuminate\Container\Attributes\CurrentUser;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\Rules\Enum;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Throwable;

#[TranslatedFormFields('users.fields.')]
final class InviteUser extends Component
{
    use WithTranslatedFields;

    #[Validate(['required', 'string', 'email', 'max:255', 'unique:users'])]
    public string $email;

    #[Validate(['required', new Enum(UserLevel::class)])]
    public string $level = UserLevel::User->value;

    public function inviteUser(#[CurrentUser] User $user): void
    {
        $this->authorize('create', User::class);

        $validated = $this->validate();
        $email = $validated['email'];

        try {
            $userLevel = UserLevel::from($validated['level']);
            Notification::route('mail', $email)->notify(new UserInvitedNotification(
                $userLevel,
                $user
            ));

            Flux::toast(__('users.invite.success_info', ['level' => $userLevel->name, 'mail' => $email]), __('general.status.success'), variant: 'success');
        } catch (Throwable $throwable) {
            Log::error('Could not invite user', ['err' => $throwable]);
            Flux::toast(__('general.error_notification', ['error' => $throwable->getMessage()]), __('general.status.error'), variant: 'danger');
        }

        Flux::modal('invite-user')->close();
    }
}
