<?php
require_once("session_config.php");
?>

<div class="topbar">

    <div class="topbar-left">
        <a href="index.php"><h2>🔐 WebCheck360</h2></a>
    </div>

    <div class="topbar-right">

        <!-- USER DROPDOWN -->
        <div class="user-dropdown" onclick="toggleMenu()">

            <div class="user-info">
                👤 <?= $_SESSION['name'] ?>
                <span class="role">(<?= $_SESSION['role'] ?>)</span>
                <span class="arrow">▾</span>
            </div>

            <div id="dropdownMenu" class="dropdown-menu">
                <a href="change_password.php">🔑 Change Password</a>
                <a href="logout.php" class="logout">🚪 Logout</a>
            </div>

        </div>

    </div>

</div>

<script>
function toggleMenu() {
    document.getElementById("dropdownMenu").classList.toggle("show");
}

// Close if clicked outside
window.onclick = function(e) {
    if (!e.target.closest('.user-dropdown')) {
        document.getElementById("dropdownMenu").classList.remove("show");
    }
}
</script>
