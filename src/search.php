<?php
declare(strict_types=1);

require_once __DIR__ . '/db_config_pdo.php';

/**
 * Encodes output for safe HTML display.
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/**
 * Escapes LIKE wildcards so user input is treated as data.
 */
function escape_like(string $value): string
{
    return str_replace(
        ['\\', '%', '_'],
        ['\\\\', '\%', '\_'],
        $value
    );
}

$keyword = $_GET['keyword'] ?? '';

if (!is_string($keyword)) {
    http_response_code(400);
    exit('Invalid keyword.');
}

$keyword = trim($keyword);

if ($keyword === '' || mb_strlen($keyword, 'UTF-8') > 100) {
    http_response_code(400);
    exit('Keyword must be between 1 and 100 characters.');
}

try {
    $stmt = $pdo->prepare(
        "SELECT id, name, illness_history
         FROM patient_records
         WHERE name LIKE :keyword ESCAPE '\\\\'
         LIMIT 50"
    );

    $stmt->execute([
        ':keyword' => '%' . escape_like($keyword) . '%'
    ]);

    $rows = $stmt->fetchAll();

    if ($rows) {
        echo "<div>Result found for keyword: " . e($keyword) . "</div><hr>";

        foreach ($rows as $row) {
            echo "<div>";
            echo "Patient: " . e((string) $row['name']);
            echo " | History: " . e((string) $row['illness_history']);
            echo "</div><hr>";
        }
    } else {
        echo "No records found for: " . e($keyword);
    }
} catch (PDOException $ex) {
    error_log('Search error: ' . $ex->getMessage());
    http_response_code(500);
    echo "Internal search error.";
}