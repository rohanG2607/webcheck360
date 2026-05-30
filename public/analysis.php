<?php
require_once(__DIR__."/../app/helpers/session_config.php");
require_once(__DIR__."/../app/helpers/db.php");
require_once(__DIR__."/../app/services/engine.php");
require_once(__DIR__."/../app/services/fetch_data.php");
require_once(__DIR__."/../modules/ai_insight_engine/engine/trust_engine.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$userId = $_SESSION['user_id'];

/* ---------- AUTOCOMPLETE ---------- */
$urlStmt = $conn->prepare("
    SELECT DISTINCT url 
    FROM speed_tests 
    WHERE user_id = ?
    ORDER BY url ASC
");
$urlStmt->bind_param("i", $userId);
$urlStmt->execute();
$urlResult = $urlStmt->get_result();

$urls = [];
while ($row = $urlResult->fetch_assoc()) {
    $urls[] = $row['url'];
}

$result = null;
$trustData = null;
$delta = null;
$deltaStatus = null;
$url = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $url = trim($_POST['url']);

    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
        $url = "http://" . $url;
    }

    /* ---------- RISK ENGINE ---------- */
    $features = getWebsiteFeatures($url);
    $riskData = calculateRisk($features);

    /* ---------- TRUST ENGINE ---------- */
    $trustData = calculateTrustScore($url);

    /* ---------- PREVIOUS SCORE ---------- */
    $prevStmt = $conn->prepare("
        SELECT risk_score
        FROM ai_analysis_history
        WHERE user_id = ?
        AND url = ?
        ORDER BY analyzed_at DESC
        LIMIT 1
    ");
    $prevStmt->bind_param("is", $userId, $url);
    $prevStmt->execute();
    $prevResult = $prevStmt->get_result();
    $prev = $prevResult->fetch_assoc();

    if ($prev) {
        $delta = $riskData['riskScore'] - $prev['risk_score'];

        if ($delta > 0) $deltaStatus = "INCREASED";
        elseif ($delta < 0) $deltaStatus = "IMPROVED";
        else $deltaStatus = "UNCHANGED";
    }

    /* ---------- SAVE RISK HISTORY ---------- */
    $reasonText = implode(" ", $riskData['recommendations']);

    $stmt = $conn->prepare("
        INSERT INTO ai_analysis_history
        (user_id, url, risk_score, risk_level, broken_ratio,
         performance_score, ttfb, page_size, https_flag, reason)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->bind_param(
        "isisdidiss",
        $userId,
        $url,
        $riskData['riskScore'],
        $riskData['level'],
        $features['broken_ratio'],
        $features['performance_score'],
        $features['ttfb'],
        $features['page_size'],
        $features['https_flag'],
        $reasonText
    );

    $stmt->execute();

    $result = $riskData;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Enterprise Risk & Trust Intelligence</title>

<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/theme.css">
<link rel="stylesheet" href="assets/css/analysis.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>
<div class="wrapper">
<?php include __DIR__.'/../app/views/partials/sidebar.php'; ?>

<div class="main">
<?php include __DIR__.'/../app/views/partials/topbar.php'; ?>

<h2>🧠 Enterprise Risk & Trust Intelligence</h2>

<div class="ai-box">
<form method="POST">
<label>Enter or Select Website</label>

<input type="text"
       name="url"
       list="tested-websites"
       placeholder="Start typing or select from tested URLs..."
       required>

<datalist id="tested-websites">
<?php foreach ($urls as $u): ?>
<option value="<?= htmlspecialchars($u) ?>">
<?php endforeach; ?>
</datalist>

<button type="submit">Run Enterprise Analysis</button>
</form>
</div>

<?php if ($result): ?>

<div class="ai-result">

<h3>Analyzed URL: <?= htmlspecialchars($url) ?></h3>

<!-- ================= RISK SECTION ================= -->

<h1>Risk Score: <?= $result['riskScore'] ?>/100</h1>

<?php if ($delta !== null): ?>
<p>
<?php if ($deltaStatus == "INCREASED"): ?>
<span style="color:#ef4444;">⬆ Risk Increased by <?= abs($delta) ?></span>
<?php elseif ($deltaStatus == "IMPROVED"): ?>
<span style="color:#22c55e;">⬇ Risk Improved by <?= abs($delta) ?></span>
<?php else: ?>
<span style="color:#94a3b8;">No change from previous analysis</span>
<?php endif; ?>
</p>
<?php endif; ?>

<h3 class="
<?= $result['level']=="SAFE"?'risk-safe':'' ?>
<?= $result['level']=="MODERATE"?'risk-moderate':'' ?>
<?= $result['level']=="HIGH RISK"?'risk-high':'' ?>
">
<?= $result['level'] ?>
</h3>

<hr>

<h3>Category Risk Scores</h3>
<ul>
<li>Structural Risk: <?= $result['structuralRisk'] ?>%</li>
<li>Performance Risk: <?= $result['performanceRisk'] ?>%</li>
<li>Security Risk: <?= $result['securityRisk'] ?>%</li>
</ul>

<hr>

<h3>Risk Confidence: <?= $result['confidence'] ?>%</h3>

<hr>

<h3>Dominant Risk Factor: <?= $result['dominantFactor'] ?></h3>

<hr>

<h3>Risk Contribution Breakdown</h3>
<div class="chart-wrapper">
<canvas id="contributionChart"></canvas>
</div>

<hr>

<h3>AI Recommendations</h3>
<ul>
<?php foreach ($result['recommendations'] as $rec): ?>
<li><?= htmlspecialchars($rec) ?></li>
<?php endforeach; ?>
</ul>

<!-- ================= TRUST SECTION ================= -->

<hr>
<h2>🔐 Trust & Authenticity Intelligence</h2>

<h1>Trust Score: <?= $trustData['trustScore'] ?>/100</h1>

<h3 class="
<?= $trustData['riskLevel']=="LOW"?'risk-safe':'' ?>
<?= $trustData['riskLevel']=="MEDIUM"?'risk-moderate':'' ?>
<?= $trustData['riskLevel']=="HIGH"?'risk-high':'' ?>
">
<?= $trustData['status'] ?>
</h3>

<p><strong>Trust Confidence:</strong> <?= $trustData['confidence'] ?>%</p>

<?php if (!empty($trustData['flags'])): ?>
<hr>
<h3>⚠ Threat Indicators</h3>
<ul>
<?php foreach ($trustData['flags'] as $flag): ?>
<li><?= htmlspecialchars($flag) ?></li>
<?php endforeach; ?>
</ul>
<?php else: ?>
<p style="color:#22c55e;">No suspicious indicators detected.</p>
<?php endif; ?>

<!-- ================= GPT BUTTON ================= -->

<hr>
<button id="deepInsightBtn" class="deep-btn">
🔍 Generate GPT Deep Insight
</button>

<div id="gptInsightBox" class="gpt-insight" style="display:none;"></div>

</div>

<script>
new Chart(document.getElementById('contributionChart'), {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_keys($result['contributions'])) ?>,
        datasets: [{
            data: <?= json_encode(array_values($result['contributions'])) ?>,
            backgroundColor: ['#38bdf8','#f59e0b','#ef4444','#22c55e','#a855f7'],
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { color: '#cbd5e1' }
            }
        }
    }
});

document.getElementById("deepInsightBtn").onclick = async function(){

    const btn = this;
    btn.innerText = "Generating Insight...";
    btn.disabled = true;

    const res = await fetch("/WebCheck360-backup/WebCheck360/modules/ai_insight_engine/generate_risk_insight.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            url: "<?= htmlspecialchars($url) ?>",
            riskScore: <?= $result['riskScore'] ?>,
            trustScore: <?= $trustData['trustScore'] ?>,
            level: "<?= $result['level'] ?>"
        })
    });

    const data = await res.json();

    const box = document.getElementById("gptInsightBox");
    box.style.display = "block";
    box.innerText = data.insight || "Insight unavailable.";

    btn.innerText = "🔍 Generate GPT Deep Insight";
    btn.disabled = false;
};
</script>

<?php endif; ?>

</div>
</div>
</body>
</html>