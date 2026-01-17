<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../lib/validator.php';
require_once __DIR__ . '/../lib/auth.php';
require_once __DIR__ . '/../lib/csrf.php';
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?php echo defined('SITE_NAME')?SITE_NAME:'HimiloGuul'; ?></title>
  <link rel="stylesheet" href="/projects/himiloGuul-php/assets/css/style.css">
</head>
<body class="<?php echo empty($_SESSION['user']) ? 'no-sidebar' : ''; ?>">
  <header class="header">
    <div class="container">
      <div class="left">
        <a class="brand" href="/projects/himiloGuul-php/index.php"><?php echo SITE_NAME; ?></a>
        <a class="home-link" href="/projects/himiloGuul-php/index.php" style="margin-left:12px;color:#fff;text-decoration:none">Home</a>
      </div>
      <div class="right">
        <?php if(!empty($_SESSION['user'])): ?>
          <span class="user-menu">Hello, <?php echo htmlspecialchars($_SESSION['user']['username']); ?></span>
          <a href="/projects/himiloGuul-php/logout.php" style="margin-left:12px;color:#fff;text-decoration:none">Logout</a>
        <?php else: ?>
          <nav>
            <a href="/projects/himiloGuul-php/register.php">Register</a>
            <a href="/projects/himiloGuul-php/login.php">Login</a>
          </nav>
        <?php endif; ?>
      </div>
    </div>
  </header>
  <main class="main-wrapper">
  <?php if (!empty($_SESSION['user']) && ($_SESSION['user']['role'] ?? '') === 'Admin' && !extension_loaded('gd')): ?>
    <div class="content" style="padding:12px 20px;margin-top:var(--header-height)">
      <div class="alert error">Warning: PHP GD extension is not enabled on this server; image thumbnailing is disabled. To fix this, enable <code>gd</code> in your <code>php.ini</code> (e.g. remove the leading semicolon from <code>extension=gd</code> or <code>extension=php_gd2.dll</code>) and restart Apache.</div>
    </div>
  <?php endif; ?>