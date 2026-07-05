<?php
$hash = password_hash('doctorsecret123', PASSWORD_ARGON2ID);
$pdo = new PDO('mysql:host=127.0.0.1;port=3306;dbname=medic_vault_db;charset=utf8mb4', 'root', '');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$stmt = $pdo->prepare("INSERT INTO staff_credentials (username, auth_key_hash, role) VALUES ('admin', :hash, 'admin')");
$stmt->execute([':hash' => $hash]);
echo "Admin user inserted.\n";
