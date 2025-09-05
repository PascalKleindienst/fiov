<?php

declare(strict_types=1);

namespace App\Actions;

use App\Enums\Color;
use App\Enums\Icon;
use App\Facades\CryptoService;
use App\Models\User;
use App\Models\Wallet;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Random\RandomException;
use SensitiveParameter;

final readonly class CreateUser
{
    /**
     * @param  array{
     *     name: string,
     *     email: string,
     *     password: string,
     *     level: string,
     * }  $data
     *
     * @throws RandomException
     */
    public function handle(#[SensitiveParameter] array $data, bool $login = false): User
    {
        // Setup data encryption keys
        $salt = CryptoService::generateSalt();
        $dek = CryptoService::generateDEK();
        $encryptedDEK = CryptoService::encryptDEK($dek, CryptoService::deriveKey($data['password'], $salt));
        Wallet::encryptUsing(CryptoService::withDEK($dek));

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'level' => $data['level'],
            'encryption_salt' => $salt,
            'encrypted_dek' => $encryptedDEK,
        ]);

        Wallet::create([
            'title' => 'Default',
            'description' => 'Default Wallet',
            'currency' => config('money.defaultCurrency'),
            'color' => Color::Blue,
            'icon' => Icon::Wallet,
            'user_id' => $user->id,
        ]);

        event(new Registered($user));

        if ($login) {
            Auth::login($user);
            session([\App\Services\CryptoService::DEK_SESSION_KEY => $dek]); // Store for later use in session (optional)
        }

        return $user;
    }
}
