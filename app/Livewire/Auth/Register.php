<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Actions\CreateUser;
use App\Enums\UserLevel;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Random\RandomException;

#[Layout('components.layouts.auth')]
final class Register extends Component
{
    #[Validate(['required', 'string', 'max:255'])]
    public string $name = '';

    #[Validate(['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class])]
    public string $email = '';

    #[Validate]
    public string $password = '';

    public string $password_confirmation = '';

    #[Validate(['required', new Rules\Enum(UserLevel::class)])]
    public string $level = UserLevel::User->value;

    public function mount(): void
    {
        $this->fill([
            'level' => request()->get('level', $this->level),
            'email' => request()->get('email', $this->email),
        ]);
    }

    /**
     * @return array<string, array<int, Rules\Password|null|string>>
     */
    public function rules(): array
    {
        return [
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ];
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws RandomException
     */
    public function register(): void
    {
        $validated = $this->validate();

        $user = (new CreateUser())->handle($validated, login: true);
        $user->markEmailAsVerified();
        $user->notify(new WelcomeNotification());

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}
