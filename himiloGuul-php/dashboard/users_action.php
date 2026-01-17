<?php
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../connection.php';
require_login();
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
  header('Location: users.php'); exit;
}

if (!verify_csrf($_POST['csrf_token'] ?? '')){
  die('Invalid CSRF');
}

$action = $_POST['action'] ?? '';
$id = intval($_POST['id'] ?? 0);
if (!$id) header('Location: users.php');

if ($action === 'delete'){
  // hard delete
  $stmt = $pdo->prepare('DELETE FROM users WHERE id = :id');
  $stmt->execute([':id'=>$id]);
} elseif ($action === 'deactivate'){
  $pdo->prepare('UPDATE users SET status = "not_active" WHERE id = :id')->execute([':id'=>$id]);
} elseif ($action === 'activate'){
  $pdo->prepare('UPDATE users SET status = "active" WHERE id = :id')->execute([':id'=>$id]);
}

header('Location: users.php'); exit;
