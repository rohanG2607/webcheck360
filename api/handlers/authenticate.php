<?php
session_start();

$conn = new mysqli("localhost", "root", "", "webcheck360");

$username = $_POST['username'];
$password = $_POST['password'];

$stmt = $conn->prepare("SELECT * FROM users WHERE username=?");
$stmt->bind_param("s", $username);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows === 1) {

    $user = $result->fetch_assoc();

    if (password_verify($password, $user['password'])) {
        $_SESSION['user'] = $username;
        header("Location: /WebCheck360-backup/WebCheck360/public/index.php");
        exit;
    }
}

header("Location: /WebCheck360-backup/WebCheck360/public/login.php?error=1");
exit;
?>
