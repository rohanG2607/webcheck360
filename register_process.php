<?php
require_once("session_config.php");
require_once("db.php");

$name     = $_POST['name'];
$email    = $_POST['email'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);

// Default role
$role = "viewer";

$stmt = $conn->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $name, $email, $password, $role);
$stmt->execute();

$userId = $stmt->insert_id;

// ✅ AUTO LOGIN AFTER REGISTER
$_SESSION['user_id'] = $userId;
$_SESSION['name']    = $name;
$_SESSION['email']   = $email;
$_SESSION['role']    = $role;

header("Location: index.php");
exit;
?>
