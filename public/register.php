<?php
require_once(__DIR__."/../app/helpers/session_config.php");

if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register | WebCheck360</title>
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="stylesheet" href="assets/css/auth.css">
</head>

<body>

<div class="login-wrapper">
<div class="login-card">

<h1>Create Account</h1>
<p class="subtitle">Register to use WebCheck360</p>

<?php if (isset($_GET['error'])): ?>
<div class="error-box">Email already exists</div>
<?php endif; ?>

<form method="post" action="/WebCheck360-backup/WebCheck360/api/handlers/register_process.php">

<label>Full Name</label>
<input type="text" name="name" placeholder="Full Name" required>

<label>Email</label>
<input type="email" name="email" placeholder="Email" required>

<label>Password</label>
<div class="password-wrapper">
    <input type="password" name="password" id="registerPassword" placeholder="Create Password" required>
    <span class="toggle-password" onclick="togglePassword('registerPassword', this)">👁</span>
</div>


<button type="submit">Register</button>

</form>

<br>
<a href="login.php">← Back to Login</a>

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
