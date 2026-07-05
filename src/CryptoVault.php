<?php
declare(strict_types=1);

/**
 * CryptoVault.php - AES-256-GCM patient record vault.
 * This file contains the testable core cryptographic logic.
 */

function getVaultKey(): string
{
    $b64 = getenv('MEDVAULT_KEY');

    if ($b64 === false || $b64 === '') {
        throw new RuntimeException('MEDVAULT_KEY is not set in the environment.');
    }

    $key = base64_decode($b64, true);

    if ($key === false || strlen($key) !== 32) {
        throw new RuntimeException('Vault key misconfigured: expected a 32-byte AES-256 key.');
    }

    return $key;
}

function vaultEncrypt(string $plaintext, string $key): string
{
    $iv = random_bytes(12);
    $tag = '';

    $ciphertext = openssl_encrypt(
        $plaintext,
        'aes-256-gcm',
        $key,
        OPENSSL_RAW_DATA,
        $iv,
        $tag,
        '',
        16
    );

    if ($ciphertext === false || strlen($tag) !== 16) {
        throw new RuntimeException('Encryption failed.');
    }

    return base64_encode($iv . $tag . $ciphertext);
}

function vaultDecrypt(string $packedB64, string $key): string
{
    $raw = base64_decode($packedB64, true);

    if ($raw === false || strlen($raw) < 28) {
        throw new RuntimeException('Malformed vault payload.');
    }

    $iv = substr($raw, 0, 12);
    $tag = substr($raw, 12, 16);
    $ciphertext = substr($raw, 28);

    $plaintext = openssl_decrypt(
        $ciphertext,
        'aes-256-gcm',
        $key,
        OPENSSL_RAW_DATA,
        $iv,
        $tag
    );

    if ($plaintext === false) {
        throw new RuntimeException('Authentication failed: payload may be tampered.');
    }

    return $plaintext;
}