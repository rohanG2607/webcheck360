<?php
require_once("session_config.php");
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/* ------------------------------
   FETCH URL LIST FOR AUTOCOMPLETE
------------------------------ */
$urlQuery = $conn->prepare("
    SELECT DISTINCT url 
    FROM speed_tests 
    WHERE user_id = ?
    ORDER BY tested_at DESC
    LIMIT 10
");

$urlQuery->bind_param("i", $_SESSION['user_id']);
$urlQuery->execute();
$result = $urlQuery->get_result();

$urls = [];
while ($row = $result->fetch_assoc()) {
    $urls[] = $row['url'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Website Speed Test</title>

    <link rel="stylesheet" href="assets/css/theme.css">
    <link rel="stylesheet" href="assets/css/layout.css">
    <link rel="stylesheet" href="assets/css/speed.css">

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<div class="wrapper">
<?php include 'sidebar.php'; ?>

<div class="main">
<?php include 'topbar.php'; ?>

<!-- Speed Test Box -->
<div class="speed-box">
    <h2>⚡ Website Speed Analyzer</h2>
    <p class="subtitle">
        Multi-sample performance benchmarking with stability scoring.
    </p>

    <div class="speed-form">

        <input id="speedUrl"
               class="speed-input"
               type="text"
               list="tested-websites"
               placeholder="Start typing or select from tested URLs..."
               autocomplete="off"
               required>

        <datalist id="tested-websites">
            <?php foreach ($urls as $u): ?>
                <option value="<?= htmlspecialchars($u) ?>">
            <?php endforeach; ?>
        </datalist>

        <button id="runSpeed" class="speed-btn">
            Run Speed Test
        </button>

    </div>
</div>

<!-- Website Card -->
<div id="websiteCard" class="metric-box website-card" style="display:none;">
    <div class="metric-title">Analyzed Website</div>
    <div id="websiteName" class="metric-value">--</div>
    <div id="websiteFullUrl" class="metric-grade"></div>
</div>

<!-- Metrics -->
<div id="speedResults" class="metrics-grid" style="display:none;">

    <div class="metric-box">
        <div class="metric-title">TTFB</div>
        <div id="ttfb" class="metric-value">--</div>
    </div>

    <div class="metric-box">
        <div class="metric-title">Load Time</div>
        <div id="load" class="metric-value">--</div>
    </div>

    <div class="metric-box">
        <div class="metric-title">Page Size</div>
        <div id="size" class="metric-value">--</div>
    </div>

    <div class="metric-box performance-score">
        <div class="metric-title">Performance Score</div>
        <div id="score" class="metric-value">--</div>
        <div id="healthBadge" class="health-badge"></div>
    </div>

    <div class="metric-box">
        <div class="metric-title">Stability</div>
        <div id="stability" class="metric-value">--</div>
    </div>

</div>

<!-- Chart -->
<div class="chart-container" style="display:none;" id="chartContainer">
    <canvas id="speedChart"></canvas>
</div>

</div>
</div>

<script>
let speedChartInstance = null;

document.getElementById("runSpeed").onclick = async () => {

    const urlInput = document.getElementById("speedUrl").value.trim();
    if(!urlInput) return alert("Enter URL");

    document.getElementById("speedResults").style.display = "grid";
    document.getElementById("chartContainer").style.display = "block";
    document.getElementById("websiteCard").style.display = "block";

    /* Show website info */
    try {
        const domain = new URL(urlInput).hostname;
        document.getElementById("websiteName").innerText = domain;
    } catch {
        document.getElementById("websiteName").innerText = urlInput;
    }

    document.getElementById("websiteFullUrl").innerText = urlInput;

    /* Fetch backend */
    const res = await fetch("run_speed_test.php", {
        method:"POST",
        headers:{'Content-Type':'application/x-www-form-urlencoded'},
        body:"url="+encodeURIComponent(urlInput)
    });

    const data = await res.json();

    if(data.error){
        alert(data.error);
        return;
    }

    /* Update Metrics */
    document.getElementById("ttfb").innerText = data.ttfb + " ms";
    document.getElementById("load").innerText = data.load + " ms";
    document.getElementById("size").innerText = data.size + " KB";
    document.getElementById("score").innerText = data.score;
    document.getElementById("stability").innerText = data.stability + "%";

    /* Update Badge */
    const badge = document.getElementById("healthBadge");
    badge.className = "health-badge " + data.grade;
    badge.innerText = data.grade.toUpperCase();

    /* Destroy previous chart */
    if(speedChartInstance){
        speedChartInstance.destroy();
    }

    /* Create Chart */
    speedChartInstance = new Chart(
        document.getElementById("speedChart"),
        {
            type:"bar",
            data:{
                labels:["TTFB (ms)","Load Time (ms)","Size (KB)"],
                datasets:[{
                    label:"Performance Metrics",
                    data:[data.ttfb, data.load, data.size]
                }]
            },
            options:{
                responsive:true,
                maintainAspectRatio:true,
                aspectRatio:1.8,
                animation:{duration:600}
            }
        }
    );
}
</script>

</body>
</html>