<?php
require_once("session_config.php");
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

/* ===============================
   FETCH RISK TREND (USER ONLY)
================================= */
$trendStmt = $conn->prepare("
    SELECT url, risk_score, risk_level, analyzed_at
    FROM ai_analysis_history
    WHERE user_id = ?
    ORDER BY analyzed_at ASC
");

$trendStmt->bind_param("i", $userId);
$trendStmt->execute();
$trendResult = $trendStmt->get_result();

$labels = [];
$scores = [];

while ($row = $trendResult->fetch_assoc()) {
    $labels[] = date("d M H:i", strtotime($row['analyzed_at']));
    $scores[] = $row['risk_score'];
}

/* ===============================
   FETCH RECENT ANALYSIS (USER ONLY)
================================= */
$tableStmt = $conn->prepare("
    SELECT url, risk_score, risk_level, analyzed_at
    FROM ai_analysis_history
    WHERE user_id = ?
    ORDER BY analyzed_at DESC
    LIMIT 10
");

$tableStmt->bind_param("i", $userId);
$tableStmt->execute();
$tableResult = $tableStmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
<title>Risk Monitoring</title>

<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/theme.css">
<link rel="stylesheet" href="assets/css/analysis.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<div class="wrapper">
<?php include 'sidebar.php'; ?>

<div class="main">
<?php include 'topbar.php'; ?>

<h2>📊 Risk Monitoring Dashboard</h2>

<!-- Risk Trend Chart -->
<div class="chart-card">
<canvas id="riskChart"></canvas>
</div>

<h3>Recent Analysis</h3>

<table class="history-table">
<tr>
<th>URL</th>
<th>Risk Score</th>
<th>Risk Level</th>
<th>Analyzed At</th>
</tr>

<?php while ($row = $tableResult->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row['url']) ?></td>
<td><?= $row['risk_score'] ?></td>
<td><?= $row['risk_level'] ?></td>
<td><?= $row['analyzed_at'] ?></td>
</tr>
<?php endwhile; ?>

</table>

</div>
</div>

<script>
new Chart(document.getElementById('riskChart'),{
    type:'line',
    data:{
        labels:<?= json_encode($labels) ?>,
        datasets:[{
            label:'Risk Score Trend',
            data:<?= json_encode($scores) ?>,
            borderColor:'#ef4444',
            backgroundColor:'rgba(239,68,68,0.1)',
            tension:.3,
            fill:true
        }]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        scales:{
            y:{
                min:0,
                max:100
            }
        }
    }
});
</script>

</body>
</html>