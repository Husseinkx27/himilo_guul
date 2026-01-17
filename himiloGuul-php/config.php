<?php
// Basic configuration for HimiloGuul
// NOTE: For production, keep secrets outside webroot and use environment variables.

define('SITE_NAME','HimiloGuul');
define('DB_HOST','localhost');
define('DB_NAME','himiloGuul');
define('DB_USER','root');
define('DB_PASS','Ahmed@181');

// Session settings
ini_set('session.cookie_httponly',1);
ini_set('session.use_strict_mode',1);
session_start();

// Session inactivity timeout (seconds)
define('SESSION_TIMEOUT',300); // 5 minutes

// Track last activity
if (!isset($_SESSION['last_activity'])) {
    $_SESSION['last_activity'] = time();
} else {
    if (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
        // expire session
        session_unset();
        session_destroy();
        session_start();
        $_SESSION['expired'] = true;
    } else {
        $_SESSION['last_activity'] = time();
    }
}

// Note: remember-me login is performed after a DB connection is available (see connection.php).

// Uploads
define('UPLOAD_DIR', __DIR__ . '/uploads');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

?>