<?php
require_once("session_config.php");
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/* Fetch previously scanned websites (from scans table ONLY) */
$sites = [];
$res = $conn->query("
    SELECT DISTINCT website 
    FROM scans 
    ORDER BY scanned_at DESC
    LIMIT 20
");

if ($res) {
    while ($row = $res->fetch_assoc()) {
        $sites[] = $row['website'];
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>WebCheck360 Dashboard</title>

    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/scanner.css">
</head>

<body>

<div class="wrapper">

<?php include 'sidebar.php'; ?>

<div class="main">

<?php include 'topbar.php'; ?>

<!-- Scanner Card -->
<div class="scan-box">
    <h2>🌐 WebCheck360 – Website Health Scanner</h2>

    <form method="post" action="run_scan.php">

        <!-- INPUT WITH SERVER-SIDE DROPDOWN -->
        <input class="scan-input"
               type="text"
               name="website"
               placeholder="https://example.com"
               list="scannedWebsites"
               required>

        <!-- DATALIST (NO AJAX) -->
        <datalist id="scannedWebsites">
            <?php foreach ($sites as $site): ?>
                <option value="<?= htmlspecialchars($site) ?>">
            <?php endforeach; ?>
        </datalist>

        <label>
            <input type="checkbox" name="demo"> Demo Mode
        </label>

        <div class="scan-actions">
            <button class="action-btn start" type="submit">Start Scan</button>
        </div>

    </form>
</div>

<!-- Progress Card -->
<div class="progress-card">

    <h3>📊 Live Scan Progress</h3>

    <div class="progress-bar">
        <div id="progressBar" class="progress-fill"></div>
    </div>

    <p id="progressText" class="progress-text">
        Waiting for scan...
    </p>

    <p><b>Current URL:</b></p>
    <div id="currentUrl" class="current-url">—</div>

    <div class="scan-actions">
        <button class="action-btn danger" onclick="stopScan()">Stop</button>
        <button class="action-btn secondary" onclick="pauseScan()">Pause</button>
        <button class="action-btn secondary" onclick="resumeScan()">Resume</button>
        <button class="action-btn secondary" onclick="resetScan()">Reset</button>
        <button class="action-btn"
                onclick="window.location.href='scan_history.php'">
            View History
        </button>
    </div>

</div>

</div>
</div>

<script>
let scanStarted = false;

function checkScan() {
    fetch("scan.lock?ts=" + Date.now())
    .then(res => {
        if (res.ok) {
            scanStarted = true;
            fetchProgress();
        }
        else if (scanStarted) {
            window.location.href = "report.php";
        }
    }).catch(()=>{});
}

function fetchProgress() {
    fetch("progress.json?ts=" + Date.now())
    .then(r => r.json())
    .then(d => {
        let percent = d.percent || 0;
        document.getElementById("progressBar").style.width = percent + "%";
        document.getElementById("progressText").innerText =
            percent + "% completed (" + d.checked + " checked)";
        document.getElementById("currentUrl").innerText =
            d.currentUrl + " [" + d.status + "]";
    }).catch(()=>{});
}

function stopScan(){ fetch("stop_scan.php"); }
function pauseScan(){ fetch("pause_scan.php"); }
function resumeScan(){ fetch("resume_scan.php"); }
function resetScan(){ window.location.href="reset_scan.php"; }

setInterval(checkScan,1000);
</script>

</body>
</html>