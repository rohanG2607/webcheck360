<?php
require_once("session_config.php");
require_once("db.php");

/* Ensure user logged in */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!$conn) {
    die("Service temporarily unavailable.");
}

$user_id = $_SESSION['user_id'];

/* Fetch scans belonging to this user */
$stmt = $conn->prepare("
    SELECT id, website, total_links, broken_links, suspect_links, health_score, scanned_at
    FROM scans
    WHERE user_id = ?
    ORDER BY scanned_at DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>Scan History | WebCheck360</title>

<link rel="stylesheet" href="assets/css/theme.css">
<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/history.css">
</head>

<body>

<div class="wrapper">
<?php include 'sidebar.php'; ?>

<div class="main">
<?php include 'topbar.php'; ?>

<h2>📊 Scan History</h2>

<?php if ($result->num_rows === 0): ?>
    <div class="empty-state">
        <p>No scans saved yet.</p>
        <a href="scanner.php" class="primary-btn">Run Your First Scan</a>
    </div>
<?php else: ?>

<table class="history-table">
<thead>
<tr>
    <th>Website</th>
    <th>Total Links</th>
    <th>Broken</th>
    <th>Suspect</th>
    <th>Health Score</th>
    <th>Scanned At</th>
    <th>Action</th>
</tr>
</thead>

<tbody>
<?php while ($row = $result->fetch_assoc()): 

/* Determine health color */
$health = $row['health_score'];

if ($health >= 80) {
    $class = "good";
} elseif ($health >= 50) {
    $class = "average";
} else {
    $class = "bad";
}
?>

<tr>
    <td><?= htmlspecialchars($row['website']) ?></td>
    <td><?= $row['total_links'] ?></td>
    <td class="bad"><?= $row['broken_links'] ?></td>
    <td class="average"><?= $row['suspect_links'] ?></td>
    <td class="<?= $class ?>"><strong><?= $health ?>%</strong></td>
    <td><?= date("d M Y, H:i", strtotime($row['scanned_at'])) ?></td>

    <!-- ✅ THIS is the fixed View Details button -->
    <td>
        <a href="view_scan.php?id=<?= $row['id'] ?>" class="view-btn">
            View Details
        </a>
    </td>
</tr>

<?php endwhile; ?>
</tbody>
</table>

<?php endif; ?>

</div>
</div>

</body>
</html>