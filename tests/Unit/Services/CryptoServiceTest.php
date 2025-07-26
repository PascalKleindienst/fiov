<?php

declare(strict_types=1);

use App\Models\User;
use App\Services\CryptoService;
use Illuminate\Support\Facades\Session;

beforeEach(function (): void {
    $this->cryptoService = new CryptoService();
    $this->user = User::factory()->create([
        'encryption_salt' => $this->cryptoService->generateSalt(),
    ]);
    $this->password = 'secure-password-123';
});

it('can generate a secure salt', function (): void {
    $salt = $this->cryptoService->generateSalt();

    expect($salt)
        ->toBeString()
        ->not->toBeEmpty()
        ->and(base64_decode((string) $salt, true))->toBeString('Salt should be valid base64');
});

it('can derive key from password and salt', function (): void {
    $salt = $this->cryptoService->generateSalt();
    $key = $this->cryptoService->deriveKey($this->password, $salt);

    // The key is a binary string, so we'll check its raw length
    expect($key)
        ->toBeString()
        ->and(strlen((string) $key))->toBe(32) // 32 bytes for AES-256
        ->and($this->cryptoService->deriveKey($this->password, $salt))->toBe($key);
});

it('can generate and encrypt/decrypt DEK', function (): void {
    // Generate a DEK
    $dek = $this->cryptoService->generateDEK();

    expect($dek)
        ->toBeString()
        ->not->toBeEmpty()
        ->and(base64_decode((string) $dek, true))->toBeString('DEK should be valid base64');

    // Derive KEK from password and salt
    $kek = $this->cryptoService->deriveKey($this->password, $this->user->encryption_salt);

    // Encrypt and decrypt DEK
    $encryptedDek = $this->cryptoService->encryptDEK($dek, $kek);
    $decryptedDek = $this->cryptoService->withKEK($kek)->decrypt($encryptedDek);

    expect($decryptedDek)->toBe($dek);
});

it('can encrypt and decrypt data', function (): void {
    $dek = $this->cryptoService->generateDEK();
    $itData = 'Sensitive information';

    // Store DEK in session
    Session::put(CryptoService::DEK_SESSION_KEY, $dek);

    // Encrypt and decrypt data
    $encrypted = $this->cryptoService->encrypt($itData);
    $decrypted = $this->cryptoService->decrypt($encrypted);

    expect($decrypted)->toBe($itData);
});

it('can handle DEK decryption with valid password', function (): void {
    // Generate a DEK and KEK
    $dek = $this->cryptoService->generateDEK();
    $kek = $this->cryptoService->deriveKey($this->password, $this->user->encryption_salt);

    // Encrypt and store DEK
    $encryptedDek = $this->cryptoService->encryptDEK($dek, $kek);
    $this->user->update(['encrypted_dek' => $encryptedDek]);

    // Test decryption
    $decryptedDek = $this->cryptoService->decryptDEK($this->password, $this->user);

    expect($decryptedDek)->toBe($dek)
        ->and(Session::get(CryptoService::DEK_SESSION_KEY))->toBe($dek);
});

it('fails for invalid password during DEK decryption', function (): void {
    // Generate a DEK and KEK
    $dek = $this->cryptoService->generateDEK();
    $kek = $this->cryptoService->deriveKey($this->password, $this->user->encryption_salt);

    // Encrypt and store DEK
    $encryptedDek = $this->cryptoService->encryptDEK($dek, $kek);
    $this->user->update(['encrypted_dek' => $encryptedDek]);

    // Test with wrong password - this should throw a DecryptException
    $this->expectException(\Illuminate\Contracts\Encryption\DecryptException::class);
    expect($dek)->not->toBe($this->cryptoService->decryptDEK('wrong-password', $this->user));
});
