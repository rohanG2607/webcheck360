<?php
require_once("session_config.php");
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!$conn) {
    die("Database unavailable.");
}

$reportFile = "report.json";
$linksFile  = "links.json";

if (!file_exists($reportFile)) {
    die("No scan report found.");
}

$data = json_decode(file_get_contents($reportFile), true);
$links = file_exists($linksFile) ? json_decode(file_get_contents($linksFile), true) : [];

$user_id      = $_SESSION['user_id'];
$website      = $data['website'] ?? '';
$totalLinks   = $data['totalLinks'] ?? 0;
$brokenLinks  = $data['brokenLinks'] ?? 0;
$suspectLinks = $data['suspectLinks'] ?? 0;

/* Calculate health */
$healthScore = 100;
if ($totalLinks > 0) {
    $healthScore = max(0, 100 - round((($brokenLinks + $suspectLinks) / $totalLinks) * 100));
}

/* Insert scan summary */
$stmt = $conn->prepare("
    INSERT INTO scans 
    (user_id, website, total_links, broken_links, suspect_links, health_score)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "isiiii",
    $user_id,
    $website,
    $totalLinks,
    $brokenLinks,
    $suspectLinks,
    $healthScore
);

$stmt->execute();

/* Get inserted scan ID */
$scan_id = $stmt->insert_id;
$stmt->close();

/* Insert each link into scan_links */
if (!empty($links)) {

    $linkStmt = $conn->prepare("
        INSERT INTO scan_links (scan_id, url, status, category)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($links as $link) {

        $url = $link['url'];
        $status = $link['status'];

        if ($status >= 400) {
            $category = "Broken";
        } elseif ($status == 401 || $status == 403) {
            $category = "Suspect";
        } else {
            $category = "Valid";
        }

        $linkStmt->bind_param("isis", $scan_id, $url, $status, $category);
        $linkStmt->execute();
    }

    $linkStmt->close();
}

header("Location: report.php?saved=1");
exit;
?>