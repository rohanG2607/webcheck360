<?php
require_once("db.php");
require_once("session_config.php");

function getWebsiteFeatures($url) {

    global $conn;

    $userId = $_SESSION['user_id'];

    /* ------------------------------
       FEATURE 1: Broken Link Ratio
       (FROM SCANS TABLE)
    ------------------------------ */

    $brokenRatio = 0;

    $scanStmt = $conn->prepare("
        SELECT total_links, broken_links
        FROM scans
        WHERE website = ?
        AND user_id = ?
        ORDER BY scanned_at DESC
        LIMIT 1
    ");

    $scanStmt->bind_param("si", $url, $userId);
    $scanStmt->execute();
    $scan = $scanStmt->get_result()->fetch_assoc();

    if ($scan && $scan['total_links'] > 0) {
        $brokenRatio = $scan['broken_links'] / $scan['total_links'];
    }

    /* ------------------------------
       FEATURE 2–4: Performance Metrics
       (USER ISOLATED)
    ------------------------------ */

    $perfStmt = $conn->prepare("
        SELECT performance_score, ttfb, page_size
        FROM speed_tests
        WHERE url = ?
        AND user_id = ?
        ORDER BY tested_at DESC
        LIMIT 1
    ");

    $perfStmt->bind_param("si", $url, $userId);
    $perfStmt->execute();
    $perf = $perfStmt->get_result()->fetch_assoc();

    $performanceScore = $perf['performance_score'] ?? 50;
    $ttfb             = $perf['ttfb'] ?? 1;
    $pageSize         = $perf['page_size'] ?? 500;

    /* ------------------------------
       FEATURE 5: HTTPS Check
    ------------------------------ */

    $httpsFlag = (strpos($url, "https://") === 0) ? 1 : 0;

    return [
        'broken_ratio'      => $brokenRatio,
        'performance_score' => $performanceScore,
        'ttfb'              => $ttfb,
        'page_size'         => $pageSize,
        'https_flag'        => $httpsFlag
    ];
}
?>