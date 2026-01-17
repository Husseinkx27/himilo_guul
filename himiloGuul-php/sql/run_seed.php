<?php
require_once __DIR__ . '/../config.php';
try {
    $pdo = new PDO('mysql:host=' . DB_HOST . ';charset=utf8mb4', DB_USER, DB_PASS, [PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
    $sql = file_get_contents(__DIR__ . '/initial_schema_filled.sql');
    if (!$sql) throw new Exception('Unable to read SQL file.');
    // WARNING: This will DROP and recreate the database defined inside the SQL file.
    echo "Running seed SQL...\n";
    $pdo->exec($sql);
    echo "SQL executed successfully.\n";
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage() . "\n";
    exit(1);
}
?>