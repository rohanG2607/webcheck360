<?php
require_once("session_config.php");
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$scan_id = intval($_GET['id'] ?? 0);

/* Fetch scan */
$stmt = $conn->prepare("SELECT * FROM scans WHERE id=? AND user_id=?");
$stmt->bind_param("ii", $scan_id, $_SESSION['user_id']);
$stmt->execute();
$scan = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$scan) die("Scan not found.");

/* Fetch links */
$stmt = $conn->prepare("SELECT * FROM scan_links WHERE scan_id=?");
$stmt->bind_param("i", $scan_id);
$stmt->execute();
$result = $stmt->get_result();
$stmt->close();

/* Count categories for chart */
$validCount = 0;
$brokenCount = 0;
$suspectCount = 0;
$allLinks = [];

while ($row = $result->fetch_assoc()) {
    $allLinks[] = $row;

    if ($row['category'] === "Broken") $brokenCount++;
    elseif ($row['category'] === "Suspect") $suspectCount++;
    else $validCount++;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Scan Details | WebCheck360</title>

<link rel="stylesheet" href="assets/css/theme.css">
<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/viewscan.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<div class="wrapper">
<?php include 'sidebar.php'; ?>

<div class="main">
<?php include 'topbar.php'; ?>

<h2 class="page-title">🔎 Scan Report</h2>

<!-- SUMMARY -->
<div class="scan-summary">
    <div class="summary-card">
        <span>Total Links</span>
        <h3><?= $scan['total_links'] ?></h3>
    </div>

    <div class="summary-card danger">
        <span>Broken</span>
        <h3><?= $scan['broken_links'] ?></h3>
    </div>

    <div class="summary-card warning">
        <span>Suspect</span>
        <h3><?= $scan['suspect_links'] ?></h3>
    </div>

    <div class="summary-card success">
        <span>Health</span>
        <h3><?= $scan['health_score'] ?>%</h3>
    </div>
</div>

<div class="scan-meta">
<strong>Website:</strong> <?= htmlspecialchars($scan['website']) ?><br>
<strong>Scanned At:</strong> <?= date("d M Y, H:i", strtotime($scan['scanned_at'])) ?>
</div>

<!-- PIE CHART -->
<div class="chart-section">
<canvas id="scanPieChart"></canvas>
</div>

<!-- TABLE -->
<div class="table-wrapper">
<table class="scan-table">
<thead>
<tr>
<th>URL</th>
<th>Status</th>
<th>Category</th>
</tr>
</thead>

<tbody>
<?php foreach ($allLinks as $row): ?>
<tr>
<td class="url"><?= htmlspecialchars($row['url']) ?></td>
<td><?= $row['status'] ?></td>
<td><span class="badge <?= strtolower($row['category']) ?>">
<?= $row['category'] ?>
</span></td>
</tr>
<?php endforeach; ?>
</tbody>

</table>
</div>

</div>
</div>

<script>
new Chart(document.getElementById('scanPieChart'), {
    type: 'doughnut',
    data: {
        labels: ['Valid','Broken','Suspect'],
        datasets: [{
            data: [<?= $validCount ?>, <?= $brokenCount ?>, <?= $suspectCount ?>],
            backgroundColor: ['#22c55e','#ef4444','#f59e0b'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,   // ✅ THIS FIXES STRETCHING
        aspectRatio: 1,              // ✅ Force perfect square

        plugins: {
            legend: {
                position: 'bottom',
                labels: {
                    color: '#e2e8f0',
                    padding: 15,
                    font: { size: 13 }
                }
            }
        }
    }
});
</script>

</body>
</html>