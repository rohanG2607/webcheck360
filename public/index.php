<?php
require_once(__DIR__."/../app/helpers/session_config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once(__DIR__."/../app/helpers/db.php");

/* ------------------------------
   SPEED TEST DISTRIBUTION
------------------------------ */

$fast=0; $avg=0; $slow=0;

$dist = $conn->query("SELECT grade, COUNT(*) as c FROM speed_tests GROUP BY grade");

while($d=$dist->fetch_assoc()){
    if($d['grade']=="FAST") $fast=$d['c'];
    if($d['grade']=="AVERAGE") $avg=$d['c'];
    if($d['grade']=="SLOW") $slow=$d['c'];
}


/* ------------------------------
   PERFORMANCE TREND
------------------------------ */

$trendQ = $conn->query("
SELECT performance_score, tested_at
FROM speed_tests
ORDER BY tested_at DESC
LIMIT 10
");

$trendScores=[];
$trendLabels=[];

while($t=$trendQ->fetch_assoc()){
    $trendScores[]=$t['performance_score'];
    $trendLabels[]=date("H:i",strtotime($t['tested_at']));
}

$trendScores=array_reverse($trendScores);
$trendLabels=array_reverse($trendLabels);


/* ------------------------------
   RISK DISTRIBUTION
------------------------------ */

$safe=0;
$moderate=0;
$high=0;

$riskDist = $conn->query("
SELECT risk_level, COUNT(*) as c
FROM ai_analysis_history
GROUP BY risk_level
");

while($r=$riskDist->fetch_assoc()){

if($r['risk_level']=="SAFE") $safe=$r['c'];
if($r['risk_level']=="MODERATE") $moderate=$r['c'];
if($r['risk_level']=="HIGH RISK") $high=$r['c'];

}


/* ------------------------------
   LATEST RISK ANALYSIS
------------------------------ */

$latestRisk = $conn->query("
SELECT url,risk_score,risk_level,analyzed_at
FROM ai_analysis_history
ORDER BY analyzed_at DESC
LIMIT 1
")->fetch_assoc();


/* ------------------------------
   TOP RISK DOMAINS
------------------------------ */

$topRisks = $conn->query("
SELECT url,risk_score
FROM ai_analysis_history
ORDER BY risk_score DESC
LIMIT 5
");


/* ------------------------------
   SUSPICIOUS WEBSITES
------------------------------ */

$suspicious = $conn->query("
SELECT url,risk_score
FROM ai_analysis_history
WHERE risk_level='HIGH RISK'
ORDER BY analyzed_at DESC
LIMIT 5
");

?>

<!DOCTYPE html>
<html>
<head>

<title>WebCheck360 Dashboard</title>

<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/theme.css">
<link rel="stylesheet" href="assets/css/index.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>

<body>

<div class="wrapper">

<?php include __DIR__.'/../app/views/partials/sidebar.php'; ?>

<div class="main">

<?php include __DIR__.'/../app/views/partials/topbar.php'; ?>


<!-- HERO -->

<section class="hero">

<h1>WebCheck360</h1>
<p>Your Website Security & Intelligence Platform</p>

<a href="scanner.php" class="start-btn">
Start New Scan
</a>

</section>


<!-- SYSTEM INTELLIGENCE -->

<h2 class="section-title">System Intelligence</h2>

<div class="dashboard-charts">

<div class="chart-card">

<h3>⚡ Speed Test Distribution</h3>

<canvas id="speedPie"></canvas>

</div>


<div class="chart-card">

<h3>📈 Performance Trend</h3>

<canvas id="trendLine"></canvas>

</div>


<div class="chart-card">

<h3>🧠 Risk Distribution</h3>

<canvas id="riskPie"></canvas>

</div>

</div>



<!-- THREAT INTELLIGENCE -->

<h2 class="section-title">Threat Intelligence</h2>

<div class="dashboard-charts">


<!-- Latest Risk -->

<div class="chart-card latest-risk-card">

<h3>🧠 Latest Risk Scan</h3>

<?php if($latestRisk): ?>

<p class="risk-url">
<?= htmlspecialchars($latestRisk['url']) ?>
</p>

<div class="risk-info">

<div>
<span>Risk Score</span>
<strong><?= $latestRisk['risk_score'] ?>/100</strong>
</div>

<div>
<span>Risk Level</span>
<strong><?= $latestRisk['risk_level'] ?></strong>
</div>

<div>
<span>Analyzed</span>
<strong><?= date("d M H:i",strtotime($latestRisk['analyzed_at'])) ?></strong>
</div>

</div>

<?php else: ?>

<p>No analysis yet.</p>

<?php endif; ?>

</div>



<!-- Top Risk Domains -->

<div class="chart-card">

<h3>⚠️ Top Risk Domains</h3>

<ul>

<?php while($r=$topRisks->fetch_assoc()): ?>

<li>

<?= htmlspecialchars($r['url']) ?>

<br>

<span style="color:#ef4444;">
Risk Score <?= $r['risk_score'] ?>
</span>

</li>

<?php endwhile; ?>

</ul>

</div>



<!-- Suspicious Websites -->

<div class="chart-card">

<h3>🚨 Suspicious Websites</h3>

<ul>

<?php while($s=$suspicious->fetch_assoc()): ?>

<li>

<?= htmlspecialchars($s['url']) ?>

<br>

<span style="color:#ef4444;">
Risk Score <?= $s['risk_score'] ?>
</span>

</li>

<?php endwhile; ?>

</ul>

</div>

</div>



<!-- SECURITY TOOLS -->

<h2 class="section-title">Security Intelligence Tools</h2>

<div class="card-grid">

<a href="scanner.php" class="card">
<h2>🔗 Link Scanner</h2>
<p>Detect broken and suspicious links across websites.</p>
</a>

<a href="speed_test.php" class="card">
<h2>⚡ Speed Analyzer</h2>
<p>Measure server response, page size and performance.</p>
</a>

<a href="analysis.php" class="card">
<h2>🧠 Risk & Trust Analyzer</h2>
<p>Advanced website risk and phishing intelligence.</p>
</a>

<a href="chatbot.php" class="card">
<h2>💬 AI Assistant</h2>
<p>Ask questions about your website security and health.</p>
</a>

</div>



<!-- CHARTS -->

<script>


/* SPEED DISTRIBUTION */

new Chart(document.getElementById('speedPie'), {

type:'doughnut',

data:{
labels:['FAST','AVERAGE','SLOW'],
datasets:[{

data:[<?= $fast ?>,<?= $avg ?>,<?= $slow ?>],

backgroundColor:['#22c55e','#f59e0b','#ef4444']

}]
}

});



/* PERFORMANCE TREND */

new Chart(document.getElementById('trendLine'),{

type:'line',

data:{

labels:<?= json_encode($trendLabels) ?>,

datasets:[{

label:'Performance Score',

data:<?= json_encode($trendScores) ?>,

borderColor:'#38bdf8',

tension:.3

}]

}

});



/* RISK DISTRIBUTION */

new Chart(document.getElementById('riskPie'),{

type:'doughnut',

data:{

labels:['SAFE','MODERATE','HIGH RISK'],

datasets:[{

data:[<?= $safe ?>,<?= $moderate ?>,<?= $high ?>],

backgroundColor:['#22c55e','#f59e0b','#ef4444']

}]

}

});


</script>

</div>
</div>

</body>
</html>