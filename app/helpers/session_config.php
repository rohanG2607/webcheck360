<?php
/* Hide errors from users */
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

/* ------------------------------
   SESSION SETTINGS (BEFORE START)
------------------------------ */

$lifetime = 60 * 60 * 24 * 7; // 7 days

if (session_status() === PHP_SESSION_NONE) {

    session_set_cookie_params([
        'lifetime' => $lifetime,
        'path'     => '/',
        'secure'   => false,  // change to true when using HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    ini_set('session.gc_maxlifetime', $lifetime);

    session_start();
}

/* ------------------------------
   AUTO LOGOUT AFTER 7 DAYS INACTIVE
------------------------------ */

if (isset($_SESSION['last_activity']) &&
    (time() - $_SESSION['last_activity'] > $lifetime)) {

    session_unset();
    session_destroy();

    header("Location: /WebCheck360-backup/WebCheck360/public/login.php?expired=1");
    exit;
}

/* Update last activity */
$_SESSION['last_activity'] = time();
?>