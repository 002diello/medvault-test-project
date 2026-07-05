<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../src/CryptoVault.php';

final class CryptoVaultTest extends TestCase
{
    private string $key;

    protected function setUp(): void
    {
        $this->key = random_bytes(32); // 256-bit test key (simulates MEDVAULT_KEY)
    }

    public function testEncryptDecryptRoundTrip(): void
    {
        $plaintext = 'DIAGNOSIS: Stage-2 Carcinoma. TREATMENT: Chemotherapy cycle 1.';
        $packed    = vaultEncrypt($plaintext, $this->key);
        $recovered = vaultDecrypt($packed, $this->key);

        $this->assertSame($plaintext, $recovered);
    }

    public function testTamperedCiphertextThrowsAeadException(): void
    {
        $packed = vaultEncrypt('sensitive record', $this->key);
        $raw    = base64_decode($packed);

        // Flip one bit in the ciphertext region to simulate tampering.
        $lastIndex = strlen($raw) - 1;
        $raw[$lastIndex] = chr(ord($raw[$lastIndex]) ^ 0xFF);
        $tampered = base64_encode($raw);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Authentication failed');
        vaultDecrypt($tampered, $this->key);
    }

    public function testCredentialHashIntegrity(): void
    {
        $hash = password_hash('doctorsecret', PASSWORD_ARGON2ID);

        $this->assertTrue(password_verify('doctorsecret', $hash));
        $this->assertFalse(password_verify('wrong-guess', $hash));
    }

    public function testMalformedPayloadIsRejected(): void
    {
        $this->expectException(RuntimeException::class);
        vaultDecrypt('not-a-valid-base64-payload', $this->key);
    }
}
