<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../connection.php';

echo "Smoke test starting...\n";
try {
    $c = $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
    echo "Users in DB: " . intval($c) . "\n";
    $b = $pdo->query('SELECT COUNT(*) FROM businesses')->fetchColumn();
    echo "Businesses in DB: " . intval($b) . "\n";
    // verify admin password
    $stmt = $pdo->prepare('SELECT password FROM users WHERE username = :u LIMIT 1');
    $stmt->execute([':u'=>'admin']);
    $hash = $stmt->fetchColumn();
    if ($hash && password_verify('admin123', $hash)){
        echo "Admin credential verification: OK\n";
    } else {
        echo "Admin credential verification: FAIL or admin user missing\n";
    }
    echo "Auth tokens count: " . intval($pdo->query('SELECT COUNT(*) FROM auth_tokens')->fetchColumn()) . "\n";
    echo "Smoke test finished.\n";
} catch (Exception $e){
    echo "Smoke test failed: " . $e->getMessage() . "\n";
}
