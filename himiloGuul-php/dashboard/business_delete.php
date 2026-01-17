<?php
require_once __DIR__ . '/../inc/header.php';
require_once __DIR__ . '/../lib/auth.php';
require_login();
require_once __DIR__ . '/../connection.php';

$id = intval($_GET['id'] ?? 0);
if ($id){
  $del = $pdo->prepare('DELETE FROM businesses WHERE id = :id');
  $del->execute([':id'=>$id]);
}
header('Location: businesses.php');
exit;