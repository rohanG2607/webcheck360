<?php
$basePath   = "D:/XAMPP/htdocs/webcheck360/";
$reportFile = $basePath . "report.json";
$linksFile  = $basePath . "links.json";

// ---------- CHECK REPORT EXISTS ----------
if (!file_exists($reportFile)) {
    die("Scan not completed yet.");
}

$data = json_decode(file_get_contents($reportFile), true);

// ---------- SAFE DATA ----------
$website      = $data['website'] ?? '';
$totalLinks   = $data['totalLinks'] ?? 0;
$brokenLinks  = $data['brokenLinks'] ?? 0;
$suspectLinks = $data['suspectLinks'] ?? 0;

// ---------- LOAD LINKS ----------
$linksData = [];
if (file_exists($linksFile)) {
    $linksData = json_decode(file_get_contents($linksFile), true);
}

// ---------- HEALTH SCORE ----------
$healthScore = 100;
if ($totalLinks > 0) {
    $healthScore = max(0, 100 - round((($brokenLinks + $suspectLinks) / $totalLinks) * 100));
}

// ---------- DB SAVE MESSAGE ----------
$saveMessage = "";
if (isset($_GET['db']) && $_GET['db'] == "success") {
    $saveMessage = "Scan data saved to database successfully!";
}
?>

<!DOCTYPE html>
<html>
<head>
<title>WebCheck360 Report</title>

<style>
body {
    font-family: Arial;
    background:#f4f6f9;
}
.container {
    width:85%;
    margin:40px auto;
    background:white;
    padding:25px;
    border-radius:10px;
}
.card {
    display:inline-block;
    width:22%;
    margin:1%;
    padding:20px;
    background:#eee;
    text-align:center;
    border-radius:8px;
}
table {
    width:100%;
    border-collapse:collapse;
    margin-top:25px;
}
th,td {
    border:1px solid #ddd;
    padding:8px;
    font-size:13px;
}
th {
    background:#2c3e50;
    color:white;
}
.valid { color:green; }
.broken { color:red; }
.suspect { color:orange; }

.btn {
    padding:8px 14px;
    background:#3498db;
    color:white;
    text-decoration:none;
    border-radius:5px;
    margin-right:10px;
}

.success {
    background:#d4edda;
    color:#155724;
    padding:10px;
    border-radius:6px;
    margin-bottom:15px;
    border:1px solid #c3e6cb;
}
</style>
</head>

<body>

<div class="container">

<h2>Website Health Audit</h2>

<!-- DB SAVE SUCCESS MESSAGE -->
<?php if (!empty($saveMessage)): ?>
<div class="success" id="saveMsg">
    <?php echo $saveMessage; ?>
</div>
<?php endif; ?>

<p><b>Website:</b> <?php echo htmlspecialchars($website); ?></p>

<div class="card">
    <h2><?php echo $totalLinks; ?></h2>
    Total Links
</div>

<div class="card">
    <h2 style="color:red"><?php echo $brokenLinks; ?></h2>
    Broken
</div>

<div class="card">
    <h2 style="color:orange"><?php echo $suspectLinks; ?></h2>
    Suspect
</div>

<div class="card">
    <h2 style="color:green"><?php echo $healthScore; ?>%</h2>
    Health Score
</div>

<br><br>

<a class="btn" href="index.php">New Scan</a>
<a class="btn" href="generate_pdf.php">Download PDF</a>
<a class="btn" href="save_to_db.php">Save to DB</a>
<a class="btn" href="scan_history.php">View History</a>

<h3>All Scanned URLs</h3>

<table>
<tr>
<th>URL</th>
<th>Status</th>
<th>Category</th>
</tr>

<?php if (!empty($linksData)): ?>
<?php foreach ($linksData as $link):

$status = (int)$link['status'];

if ($status >= 404) {
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
<td><?php echo htmlspecialchars($link['url']); ?></td>
<td><?php echo $status; ?></td>
<td class="<?php echo $class; ?>"><?php echo $category; ?></td>
</tr>

<?php endforeach; ?>
<?php else: ?>
<tr>
<td colspan="3">No links captured.</td>
</tr>
<?php endif; ?>

</table>

</div>

<script>
// Auto-hide DB success message after 60 sec
setTimeout(() => {
    let msg = document.getElementById("saveMsg");
    if (msg) msg.style.display = "none";
}, 60000);
</script>

</body>
</html>
