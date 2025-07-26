<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Encryption\Encrypter;
use Illuminate\Support\Facades\Session;
use Random\RandomException;
use SensitiveParameter;

/**
 * - DEK (Data Encryption Key)    Randomly generated per-user key used to encrypt all data
 * - KEK (Key Encryption Key)    Derived from user's password, used to encrypt the DEK
 */
final readonly class CryptoService
{
    public const DEK_SESSION_KEY = 'data_encryption_key';

    private string $cipher;

    public function __construct()
    {
        $this->cipher = config('app.cipher');
    }

    /**
     * Decrypt the DEK (Data Encryption Key) using the users password
     */
    public function decryptDEK(#[SensitiveParameter] string $password, User $user): string
    {
        $decryptedDEK = $this
            ->withKEK($this->deriveKey($password, $user->encryption_salt))
            ->decrypt($user->encrypted_dek);

        if (! $decryptedDEK) {
            throw new DecryptException('Could not decrypt DEK');
        }

        Session::put(self::DEK_SESSION_KEY, $decryptedDEK); // In-memory (session-based) key usage

        return $decryptedDEK;
    }

    public function decrypt(string $ciphertext): mixed
    {
        return $this->withDEK()->decrypt($ciphertext);
    }

    public function withDEK(#[SensitiveParameter] ?string $dek = null): Encrypter
    {
        if ($dek !== null) {
            Session::put(self::DEK_SESSION_KEY, $dek);

            return new Encrypter(base64_decode($dek), $this->cipher);
        }

        $dek = base64_decode((string) Session::get(self::DEK_SESSION_KEY, $this->generateDEK()));

        return new Encrypter($dek, $this->cipher);
    }

    /**
     * Generate the DEK (Data Encryption Key)
     *
     * Randomly generated per-user key used to encrypt all data
     */
    public function generateDEK(): string
    {
        return base64_encode(Encrypter::generateKey($this->cipher));
    }

    public function withKEK(#[SensitiveParameter] string $kek): Encrypter
    {
        return new Encrypter($kek, $this->cipher);
    }

    /**
     * Generate the KEK (Key Encryption Key)
     *
     * Derived from user's password, used to encrypt the DEK
     */
    public function deriveKey(#[SensitiveParameter] string $password, string $salt): string
    {
        return hash_pbkdf2('sha256', $password, base64_decode($salt), 100000, 32, true);
    }

    /**
     * Per-user salt used to derive the KEK (stored in plaintext)
     *
     * @throws RandomException
     */
    public function generateSalt(): string
    {
        return base64_encode(random_bytes(16));
    }

    /**
     * Encrypt the DEK (Data Encryption Key) with the KEK (Key Encryption Key)
     */
    public function encryptDEK(#[SensitiveParameter] string $dek, #[SensitiveParameter] string $kek): string
    {
        return $this->withKEK($kek)->encrypt($dek);
    }

    public function encrypt(string $plaintext): string
    {
        return $this->withDEK()->encrypt($plaintext);
    }
}
