<?php

declare(strict_types=1);

namespace App\Livewire\Auth;

use App\Facades\CryptoService;
use App\Models\User;
use App\Notifications\WelcomeNotification;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.auth')]
final class Register extends Component
{
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $password = $validated['password'];
        $salt = CryptoService::generateSalt();
        $dek = CryptoService::generateDEK();
        $encryptedDEK = CryptoService::encryptDEK($dek, CryptoService::deriveKey($password, $salt));

        $validated['encryption_salt'] = $salt;
        $validated['encrypted_dek'] = $encryptedDEK;
        $validated['password'] = Hash::make($validated['password']);

        event(new Registered(($user = User::create($validated))));

        $user->notify(new WelcomeNotification());

        Auth::login($user);
        session([\App\Services\CryptoService::DEK_SESSION_KEY => $dek]); // Store for later use in session (optional)

        $this->redirect(route('dashboard', absolute: false), navigate: true);
    }
}
