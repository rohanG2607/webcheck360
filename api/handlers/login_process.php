<?php
require_once(__DIR__."/../../app/helpers/session_config.php"); // starts session safely
require_once(__DIR__."/../../app/helpers/db.php");

/* ------------------------------
   VALIDATE REQUEST METHOD
------------------------------ */
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: /WebCheck360-backup/WebCheck360/public/login.php");
    exit;
}

/* ------------------------------
   SANITIZE INPUT
------------------------------ */
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    header("Location: /WebCheck360-backup/WebCheck360/public/login.php?error=empty");
    exit;
}

/* ------------------------------
   FETCH USER SECURELY
------------------------------ */
$stmt = $conn->prepare("
    SELECT id, name, email, password, role 
    FROM users 
    WHERE email=? 
    LIMIT 1
");
$stmt->bind_param("s", $email);
$stmt->execute();

$result = $stmt->get_result();
$user = $result->fetch_assoc();

/* ------------------------------
   VERIFY PASSWORD
------------------------------ */
if ($user && password_verify($password, $user['password'])) {

    /* Prevent Session Fixation */
    session_regenerate_id(true);

    /* Store user data in session */
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['name']    = $user['name'];
    $_SESSION['email']   = $user['email'];
    $_SESSION['role']    = $user['role'];
    $_SESSION['logged_in'] = true;
    $_SESSION['last_activity'] = time();

    /* ------------------------------
       ✅ RECORD LOGIN ACTIVITY
    ------------------------------ */

    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';

    $logStmt = $conn->prepare("
        INSERT INTO login_logs (user_id, ip_address)
        VALUES (?, ?)
    ");
    $logStmt->bind_param("is", $user['id'], $ipAddress);
    $logStmt->execute();

    /* Redirect to dashboard */
    header("Location: /WebCheck360-backup/WebCheck360/public/index.php");
    exit;
}
else {
    /* Optional: small delay to slow brute force */
    sleep(1);

    header("Location: /WebCheck360-backup/WebCheck360/public/login.php?error=invalid");
    exit;
}
?>