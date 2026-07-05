# MedVault Crypto Test Project

## Requirements
- PHP 8.1+ with the `openssl` and `mbstring` extensions (bundled by default on most installs)
- PHPUnit 9.x or 10.x (installed via Composer, or your OS package manager)

## Quick start

### Option A — Composer (recommended, matches the report's PHPUnit examples)
```bash
composer require --dev phpunit/phpunit ^10
./vendor/bin/phpunit --testdox tests/CryptoVaultTest.php
```

### Option B — OS package manager (no Composer needed)
```bash
# Debian/Ubuntu
sudo apt-get install php-cli php-mbstring phpunit
phpunit --testdox tests/CryptoVaultTest.php
```

### Option C — Standalone PHPUnit PHAR
```bash
wget https://phar.phpunit.de/phpunit-10.phar
php phpunit-10.phar --testdox tests/CryptoVaultTest.php
```

## What's tested (tests/CryptoVaultTest.php)
1. `testEncryptDecryptRoundTrip` — encrypt then decrypt returns the exact original plaintext.
2. `testTamperedCiphertextThrowsAeadException` — flipping one bit in the ciphertext causes
   decryption to throw `RuntimeException`, proving the GCM authentication tag is actually checked.
3. `testCredentialHashIntegrity` — Argon2id `password_verify()` accepts the correct key and
   rejects an incorrect one.
4. `testMalformedPayloadIsRejected` — garbage input is rejected safely instead of crashing.

Take a screenshot of the terminal output (with your system clock/timestamp visible) once all
tests show green ✔ marks
