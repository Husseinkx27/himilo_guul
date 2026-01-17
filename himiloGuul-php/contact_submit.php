<?php
require_once __DIR__ . '/inc/header.php';
require_once __DIR__ . '/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: /projects/himiloGuul-php/index.php');
  exit;
}
// CSRF
if (!verify_csrf($_POST['csrf_token'] ?? '')){
  $_SESSION['flash_error'] = 'Invalid form submission.';
  header('Location: /projects/himiloGuul-php/business.php?id=' . intval($_POST['business_id'] ?? 0));
  exit;
}
$business_id = intval($_POST['business_id'] ?? 0);
$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$phone = trim($_POST['phone'] ?? '');
$message = trim($_POST['message'] ?? '');
if (!$business_id || !$name || !$email || !is_valid_email($email)){
  $_SESSION['flash_error'] = 'Please fill required fields correctly.';
  header('Location: /projects/himiloGuul-php/business.php?id=' . $business_id);
  exit;
}
$from_user = $_SESSION['user']['id'] ?? null;
$ins = $pdo->prepare('INSERT INTO contacts (business_id,from_user_id,name,email,phone,message) VALUES (:b,:f,:n,:e,:p,:m)');
$ins->execute([':b'=>$business_id,':f'=>$from_user,':n'=>$name,':e'=>$email,':p'=>$phone,':m'=>$message]);
$_SESSION['flash_success'] = 'Your message has been sent to the seller.';
header('Location: /projects/himiloGuul-php/business.php?id=' . $business_id);
exit;