<?php
set_time_limit(0);

if (!isset($_POST['website']) || empty($_POST['website'])) {
    die("No website provided");
}

$url = trim($_POST['website']);
$website = escapeshellarg($url);

/* -------- DEMO FLAG -------- */
$demoFlag = isset($_POST['demo']) ? " demo" : "";

/* -------- PATHS -------- */
$basePath = "D:/XAMPP/htdocs/webcheck360/";
$jarPath  = "F:/Eclipce/WEB_CHECK360/automation-engine/target/automation-engine-1.0-SNAPSHOT.jar";

/* -------- PREVENT MULTIPLE SCANS -------- */
if (file_exists($basePath . "scan.lock")) {
    die("Scan already running");
}

/* -------- CLEAN OLD FILES -------- */
$files = ["progress.json","report.json","links.json","stop.flag","pause.flag","scan.lock"];
foreach ($files as $file) {
    $path = $basePath.$file;
    if (file_exists($path)) unlink($path);
}

/* -------- CREATE LOCK -------- */
file_put_contents($basePath . "scan.lock", "running");

/* -------- SILENT BACKGROUND EXECUTION -------- */
/*
start /B runs in background
No visible CMD window
Buttons keep working
*/

$command = 'start /B "" java -jar "'.$jarPath.'" '.$website.$demoFlag;
pclose(popen($command, "r"));

header("Location: scanner.php");
exit;
?>