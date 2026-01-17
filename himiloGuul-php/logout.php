<?php
require_once __DIR__ . '/inc/header.php';
// destroy session and redirect
logout_user();
setcookie(session_name(), '', time() - 3600, '/');
header('Location: /projects/himiloGuul-php/index.php');
exit;