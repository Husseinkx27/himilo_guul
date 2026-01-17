<?php
require_once __DIR__ . '/../connection.php';
$pwd = 'admin123';
$hash = password_hash($pwd, PASSWORD_DEFAULT);
$stmt = $pdo->prepare('UPDATE users SET password = :p WHERE username = :u');
$stmt->execute([':p'=>$hash,':u'=>'admin']);
echo "Admin password set to 'admin123'\n";
?>