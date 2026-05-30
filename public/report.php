<?php
require_once(__DIR__."/../app/helpers/session_config.php");

$reportFile = "D:/XAMPP/htdocs/webcheck360/report.json";
$linksFile  = "D:/XAMPP/htdocs/webcheck360/links.json";

/* ------------------------------
   HANDLE NO REPORT CASE
------------------------------ */
if (!file_exists($reportFile)) {
?>
<!DOCTYPE html>
<html>
<head>
    <title>No Report | WebCheck360</title>
    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="stylesheet" href="assets/css/layout.css">
</head>

<body>

<div class="wrapper">

<?php include(__DIR__."/../app/views/partials/sidebar.php"); ?>

<div class="main">
<?php include(__DIR__."/../app/views/partials/topbar.php"); ?>

<div class="content">
    <div class="empty-state">
        <h2>📄 No Scan Report Available</h2>
        <p>You haven't scanned any website yet.</p>
        <p>Run your first scan to generate a security report.</p>

        <a href="scanner.php" class="primary-btn">
            🚀 Start New Scan
        </a>
    </div>
</div>

</div>
</div>

</body>
</html>
<?php
exit;
}

/* ------------------------------
   LOAD REPORT DATA
------------------------------ */
$data = json_decode(file_get_contents($reportFile), true);

$website      = $data['website'] ?? '';
$totalLinks   = $data['totalLinks'] ?? 0;
$brokenLinks  = $data['brokenLinks'] ?? 0;
$suspectLinks = $data['suspectLinks'] ?? 0;

/* ------------------------------
   LOAD LINKS SAFELY
------------------------------ */
$linksData = [];
if (file_exists($linksFile)) {
    $decoded = json_decode(file_get_contents($linksFile), true);
    if (is_array($decoded)) {
        $linksData = $decoded;
    }
}

/* ------------------------------
   HEALTH SCORE
------------------------------ */
$healthScore = 100;
if ($totalLinks > 0) {
    $healthScore = max(0, 100 - round((($brokenLinks + $suspectLinks) / $totalLinks) * 100));
}

$savedMessage = isset($_GET['saved']) && $_GET['saved'] == 1;
?>

<!DOCTYPE html>
<html>
<head>
<title>WebCheck360 Report</title>

<link rel="stylesheet" href="assets/css/theme.css">
<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/report.css">

</head>

<body>

<div class="wrapper">

<?php include(__DIR__."/../app/views/partials/sidebar.php"); ?>

<div class="main">

<?php include(__DIR__."/../app/views/partials/topbar.php"); ?>

<div class="content">

<h2>Website Health Audit</h2>

<?php if ($savedMessage): ?>
<div class="alert-success">
    ✅ Scan results successfully saved to database.
</div>
<?php endif; ?>

<p><b>Website:</b> <?php echo htmlspecialchars($website); ?></p>

<!-- SUMMARY CARDS -->
<div class="cards">
<div class="card">
<h2><?php echo $totalLinks; ?></h2>
<p>Total Links</p>
</div>

<div class="card">
<h2 class="red"><?php echo $brokenLinks; ?></h2>
<p>Broken</p>
</div>

<div class="card">
<h2 class="orange"><?php echo $suspectLinks; ?></h2>
<p>Suspect</p>
</div>

<div class="card">
<h2 class="green"><?php echo $healthScore; ?>%</h2>
<p>Health</p>
</div>
</div>

<br>

<a class="btn" href="scanner.php">Scan Again</a>
<a class="btn" href="/WebCheck360-backup/WebCheck360/modules/reports/generate_pdf.php">Download PDF</a>
<a class="btn" href="/WebCheck360-backup/WebCheck360/modules/website_scanner/save_to_db.php">Save to DB</a>
<a class="btn" href="/WebCheck360-backup/WebCheck360/public/history/scan_history.php">View History</a>

<br><br><br>
<h3>All Scanned URLs</h3>

<table>
<tr>
<th>URL</th>
<th>Status</th>
<th>Category</th>
</tr>

<?php if (!empty($linksData)): ?>
<?php foreach ($linksData as $link):

$status = $link['status'] ?? 0;

if ($status >= 400) {
    $category = "Broken";
    $class = "broken";
}
elseif ($status == 401 || $status == 403) {
    $category = "Suspect";
    $class = "suspect";
}
else {
    $category = "Valid";
    $class = "valid";
}
?>

<tr>
<td><?php echo htmlspecialchars($link['url'] ?? ''); ?></td>
<td><?php echo $status; ?></td>
<td class="<?php echo $class; ?>"><?php echo $category; ?></td>
</tr>

<?php endforeach; ?>
<?php else: ?>
<tr>
<td colspan="3">No link data available.</td>
</tr>
<?php endif; ?>

</table>

</div>
</div>
</div>

<script>
if (window.location.search.includes("saved=1")) {
    setTimeout(() => {
        window.history.replaceState({}, document.title, "report.php");
    }, 2500);
}
</script>

</body>
</html>