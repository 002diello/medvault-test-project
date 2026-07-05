<?php
declare(strict_types=1);

require_once __DIR__ . '/db_config_pdo.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Method not allowed.');
}

$username = $_POST['username'] ?? '';
$inputKey = $_POST['auth_key'] ?? '';

if (!is_string($username) || !is_string($inputKey)) {
    http_response_code(400);
    exit('Invalid request.');
}

$username = trim($username);

$charLength = mb_strlen($inputKey, 'UTF-8');
$byteLength = strlen($inputKey);

if ($charLength < 12 || $charLength > 128 || $byteLength > 512) {
    http_response_code(400);
    exit('Invalid authentication key boundary.');
}

try {
    $stmt = $pdo->prepare(
        "SELECT id, username, auth_key_hash, role
         FROM staff_credentials
         WHERE username = :username
         LIMIT 1"
    );

    $stmt->execute([
        ':username' => $username
    ]);

    $user = $stmt->fetch();

    if (!$user || !password_verify($inputKey, (string) $user['auth_key_hash'])) {
        http_response_code(401);
        exit('Access denied.');
    }

    if (password_needs_rehash((string) $user['auth_key_hash'], PASSWORD_ARGON2ID, [
        'memory_cost' => 1 << 16,
        'time_cost'   => 3,
        'threads'     => 2
    ])) {
        $newHash = password_hash($inputKey, PASSWORD_ARGON2ID, [
            'memory_cost' => 1 << 16,
            'time_cost'   => 3,
            'threads'     => 2
        ]);

        $update = $pdo->prepare(
            "UPDATE staff_credentials
             SET auth_key_hash = :hash
             WHERE id = :id"
        );

        $update->execute([
            ':hash' => $newHash,
            ':id'   => $user['id']
        ]);
    }

    echo "Access Granted.";
} catch (PDOException $ex) {
    error_log('Authentication error: ' . $ex->getMessage());
    http_response_code(500);
    echo "Internal authentication error.";
}