<?php
require_once("session_config.php");
require_once("db.php");

/* Allow only admins */
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: index.php");
    exit;
}

$result = $conn->query("
SELECT users.name, users.email, login_logs.login_time, login_logs.ip_address
FROM login_logs
JOIN users ON users.id = login_logs.user_id
ORDER BY login_time DESC
");
?>

<!DOCTYPE html>
<html>
<head>
<title>Login Activity | WebCheck360</title>

<link rel="stylesheet" href="assets/css/theme.css">
<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/history.css">
</head>

<body>

<div class="wrapper">
<?php include 'sidebar.php'; ?>

<div class="main">
<?php include 'topbar.php'; ?>

<h2>📋 User Login Activity</h2>

<?php if ($result->num_rows === 0): ?>
    <div class="empty-state">
        <p>No login activity recorded yet.</p>
    </div>
<?php else: ?>

<table class="history-table">
<thead>
<tr>
    <th>User</th>
    <th>Email</th>
    <th>Login Time</th>
    <th>IP Address</th>
</tr>
</thead>

<tbody>
<?php while($row = $result->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row['name']) ?></td>
<td><?= htmlspecialchars($row['email']) ?></td>
<td><?= date("d M Y, H:i:s", strtotime($row['login_time'])) ?></td>
<td><?= htmlspecialchars($row['ip_address']) ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>

<?php endif; ?>

</div>
</div>

</body>
</html>