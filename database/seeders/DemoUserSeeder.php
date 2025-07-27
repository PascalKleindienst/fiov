<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Facades\CryptoService;
use App\Models\RecurringTransaction;
use App\Models\User;
use App\Models\Wallet;
use App\Models\WalletCategory;
use App\Models\WalletTransaction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class DemoUserSeeder extends Seeder
{
    public function run(): void
    {
        $password = 'demo';
        $salt = CryptoService::generateSalt();
        $dek = CryptoService::generateDEK();
        $encryptedDEK = CryptoService::encryptDEK($dek, CryptoService::deriveKey($password, $salt));
        $encrypter = CryptoService::withDEK($dek);
        RecurringTransaction::encryptUsing($encrypter);
        Wallet::encryptUsing($encrypter);
        WalletTransaction::encryptUsing($encrypter);
        WalletCategory::encryptUsing($encrypter);

        // Create a demo user
        User::factory()->create([
            'name' => 'Demo User',
            'email' => 'demo@localhost.com',
            'password' => Hash::make($password),
            'encryption_salt' => $salt,
            'encrypted_dek' => $encryptedDEK,
        ]);
    }
}
