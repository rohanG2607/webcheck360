<?php
require_once("session_config.php");
require_once("db.php");

header("Content-Type: application/json");

if (ob_get_length()) ob_clean();

/* ---------- VALIDATE ---------- */
if (!isset($_POST['url']) || empty($_POST['url'])) {
    echo json_encode(["error" => "No URL provided"]);
    exit;
}

$url = trim($_POST['url']);

if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
    $url = "https://" . $url;
}

/* ---------- MULTI RUN TEST ---------- */
$runs = 3;

$ttfbList = [];
$loadList = [];
$sizeList = [];

for ($i = 0; $i < $runs; $i++) {

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_USERAGENT => "Mozilla/5.0"
    ]);

    $start = microtime(true);
    $response = curl_exec($ch);
    $info = curl_getinfo($ch);
    $end = microtime(true);

    if ($response === false) {
        echo json_encode(["error" => "Website unreachable"]);
        exit;
    }

    curl_close($ch);

    $ttfbList[] = $info['starttransfer_time'] * 1000;
    $loadList[] = ($end - $start) * 1000;
    $sizeList[] = strlen($response) / 1024;
}

/* ---------- CALCULATE AVERAGES ---------- */
$avgTtfb  = round(array_sum($ttfbList) / $runs, 2);
$avgLoad  = round(array_sum($loadList) / $runs, 2);
$avgSize  = round(array_sum($sizeList) / $runs, 2);

$minLoad = round(min($loadList), 2);
$maxLoad = round(max($loadList), 2);

/* ---------- STABILITY SCORE ---------- */
$variation = $maxLoad - $minLoad;
$stability = 100 - min(100, ($variation / $avgLoad) * 100);
$stability = round($stability, 2);

/* ---------- PERFORMANCE SCORE ---------- */
$score = 100;

if ($avgLoad > 3000) $score -= 30;
if ($avgTtfb > 800) $score -= 20;
if ($avgSize > 1500) $score -= 15;

$score = max(10, $score);

/* ---------- GRADE ---------- */
$grade = "slow";
if ($score >= 80) $grade = "excellent";
else if ($score >= 60) $grade = "good";
else if ($score >= 40) $grade = "average";

/* ---------- SAVE TO DB ---------- */
if (isset($_SESSION['user_id'])) {

    $stmt = $conn->prepare("
        INSERT INTO speed_tests
        (user_id, url, load_time, ttfb, page_size, performance_score, grade)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");

    if ($stmt) {
        $stmt->bind_param(
            "isdddis",
            $_SESSION['user_id'],
            $url,
            $avgLoad,
            $avgTtfb,
            $avgSize,
            $score,
            $grade
        );

        $stmt->execute();
    }
}

/* ---------- RETURN JSON ---------- */
echo json_encode([
    "ttfb" => $avgTtfb,
    "load" => $avgLoad,
    "size" => $avgSize,
    "score" => $score,
    "grade" => $grade,
    "stability" => $stability,
    "minLoad" => $minLoad,
    "maxLoad" => $maxLoad
]);
exit;
?>