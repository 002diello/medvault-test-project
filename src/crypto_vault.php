<?php
declare(strict_types=1);

require_once __DIR__ . '/bootstrap.php';
require_once __DIR__ . '/CryptoVault.php';

/**
 * crypto_vault.php
 * HTTP endpoint for encrypting medical payloads.
 * The cryptographic logic is handled by CryptoVault.php.
 */

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    try {
        $medicalPayload = $_POST['payload'] ?? '';

        if (!is_string($medicalPayload) || $medicalPayload === '') {
            http_response_code(400);
            exit('Invalid payload.');
        }

        $key = getVaultKey();
        $encrypted = vaultEncrypt($medicalPayload, $key);

        header('Content-Type: application/json');

        echo json_encode([
            'status' => 'vaulted',
            'data'   => $encrypted
        ]);
    } catch (Throwable $ex) {
        error_log('Crypto vault error: ' . $ex->getMessage());

        http_response_code(500);

        echo json_encode([
            'status'  => 'error',
            'message' => 'Cryptographic operation failed safely.'
        ]);
    }
}
