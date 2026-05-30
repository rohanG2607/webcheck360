<?php
require_once(__DIR__."/../../app/helpers/session_config.php");
require_once(__DIR__."/../../app/helpers/db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: /WebCheck360-backup/WebCheck360/public/login.php");
    exit;
}

$userId = $_SESSION['user_id'];

$current = $_POST['current_password'];
$new     = $_POST['new_password'];
$confirm = $_POST['confirm_password'];

// Check if new passwords match
if ($new !== $confirm) {
    header("Location: /WebCheck360-backup/WebCheck360/public/change_password.php?error=1");
    exit;
}

// Fetch existing password hash
$stmt = $conn->prepare("SELECT password FROM users WHERE id=?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Verify current password
if (!password_verify($current, $user['password'])) {
    header("Location: /WebCheck360-backup/WebCheck360/public/change_password.php?error=1");
    exit;
}

// Hash new password
$newHash = password_hash($new, PASSWORD_DEFAULT);

// Update password
$update = $conn->prepare("UPDATE users SET password=? WHERE id=?");
$update->bind_param("si", $newHash, $userId);
$update->execute();

header("Location: /WebCheck360-backup/WebCheck360/public/change_password.php?success=1");
exit;
?>
