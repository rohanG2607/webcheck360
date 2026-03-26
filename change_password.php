<?php
require_once("session_config.php");
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Change Password | WebCheck360</title>
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>

<body>

<div class="login-wrapper">
<div class="login-card">

<h1>Change Password</h1>

<?php if (isset($_GET['success'])): ?>
<div class="success-box">Password updated successfully</div>
<?php endif; ?>

<?php if (isset($_GET['error'])): ?>
<div class="error-box">Current password is incorrect</div>
<?php endif; ?>

<form method="post" action="change_password_process.php">

<label>Current Password</label>
<div class="password-wrapper">
    <input type="password" name="current_password" id="currentPassword"  placeholder="Current Password" required>
    <span class="toggle-password" onclick="togglePassword('currentPassword', this)">👁</span>
</div>

<label>New Password</label>
<div class="password-wrapper">
    <input type="password" name="new_password" id="newPassword" placeholder="New Password" required>
    <span class="toggle-password" onclick="togglePassword('newPassword', this)">👁</span>
</div>

<label>Confirm Password</label>
<div class="password-wrapper">
    <input type="password" name="confirm_password" id="confirmPassword" placeholder="Confirm Password" required>
    <span class="toggle-password" onclick="togglePassword('confirmPassword', this)">👁</span>
</div>


<button type="submit">Update Password</button>

</form>

<br>
<a href="index.php">← Back to Dashboard</a>

</div>
</div>

<script>
function togglePassword(fieldId, icon) {

    const field = document.getElementById(fieldId);

    if (field.type === "password") {
        field.type = "text";
        icon.textContent = "👁️";
    } else {
        field.type = "password";
        icon.textContent = "👁";
    }
}
</script>

</body>
</html>
