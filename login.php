<?php
require_once("session_config.php");

// If already logged in → go to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login | WebCheck360</title>

    <!-- Global Theme -->
    <link rel="stylesheet" href="assets/css/theme.css">

    <!-- Auth Page CSS -->
    <link rel="stylesheet" href="assets/css/auth.css">
</head>

<body>

<div class="login-wrapper">

    <div class="login-card">

        <h1>WebCheck360</h1>
        <p class="subtitle">Website Security & Health Monitor</p>

        <?php if (isset($_GET['error'])): ?>
            <div class="error-box">
                Invalid Email or Password
            </div>
        <?php endif; ?>

        <form method="post" action="login_process.php">

            <label>Email</label>
            <input type="email" name="email" placeholder="Email" required>

            <label>Password</label>
            <div class="password-wrapper">
    <input type="password" name="password" id="loginPassword" placeholder="Password" required>
    <span class="toggle-password" onclick="togglePassword('loginPassword', this)">👁</span>
</div>


            <button type="submit">Login</button>

        </form>

        <br>
<a href="register.php">Create New Account</a>


        <div class="login-footer">
            © <?= date("Y") ?> WebCheck360
        </div>

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
