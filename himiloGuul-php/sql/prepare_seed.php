<?php
// prepare_seed.php
// Generates an SQL file with password hashes filled in for demo accounts.
$input = __DIR__ . '/initial_schema.sql';
$output = __DIR__ . '/initial_schema_filled.sql';
if (!file_exists($input)) {
    die("Cannot find $input\n");
}
$sql = file_get_contents($input);
$demoPasswords = [
    password_hash('admin123', PASSWORD_DEFAULT),
    password_hash('seller123', PASSWORD_DEFAULT),
    password_hash('buyer123', PASSWORD_DEFAULT),
];
foreach ($demoPasswords as $hash) {
    $sql = preg_replace('/<REPLACE_WITH_PASSWORD_HASH>/', addslashes($hash), $sql, 1);
}
file_put_contents($output, $sql);
echo "Wrote $output with demo password hashes (admin123, seller123, buyer123).\n";
echo "Please review the file before importing to your DB.\n";