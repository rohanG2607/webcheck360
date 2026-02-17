<?php
$conn = new mysqli("localhost", "root", "", "webcheck360");

if ($conn->connect_error) {
    die("DB Connection Failed");
}

/* -----------------------------
   LOAD SUMMARY (report.json)
------------------------------*/
$reportPath = "D:/XAMPP/htdocs/webcheck360/report.json";

if (!file_exists($reportPath)) {
    die("report.json not found.");
}

$report = json_decode(file_get_contents($reportPath), true);

/* -----------------------------
   INSERT INTO scans TABLE
------------------------------*/
$stmt = $conn->prepare("
    INSERT INTO scans (website, total_links, broken_links, suspect_links, scan_time)
    VALUES (?, ?, ?, ?, NOW())
");

$stmt->bind_param(
    "siii",
    $report['website'],
    $report['totalLinks'],
    $report['brokenLinks'],
    $report['suspectLinks']
);

$stmt->execute();
$scanId = $stmt->insert_id;

/* -----------------------------
   LOAD LINKS (links.json)
------------------------------*/
$linksPath = "D:/XAMPP/htdocs/webcheck360/links.json";

if (!file_exists($linksPath)) {
    die("links.json not found.");
}

$linksData = json_decode(file_get_contents($linksPath), true);

if (!$linksData || count($linksData) === 0) {
    die("No links inside links.json");
}

/* -----------------------------
   INSERT LINKS INTO scan_links
------------------------------*/
$linkStmt = $conn->prepare("
    INSERT INTO scan_links (scan_id, url, status_code)
    VALUES (?, ?, ?)
");

foreach ($linksData as $link) {

    $url = $link['url'];
    $status = $link['status'];

    $linkStmt->bind_param("isi", $scanId, $url, $status);
    $linkStmt->execute();
}

header("Location: report.php?db=success");
exit;
?>
