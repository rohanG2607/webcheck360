<?php
set_time_limit(0);

if (!isset($_POST['website']) || empty($_POST['website'])) {
    die("No website provided");
}

$website = escapeshellarg($_POST['website']);

// -------- DEMO MODE FLAG --------
$demoFlag = "";
if (isset($_POST['demo'])) {
    $demoFlag = " demo";
}

// -------- PATHS --------
$basePath = "D:/XAMPP/htdocs/webcheck360/";
$jarPath  = "F:/Eclipce/WEB_CHECK360/automation-engine/target/automation-engine-1.0-SNAPSHOT.jar";

// Files created by Java
$files = [
    "progress.json",
    "report.json",
    "links.json",
    "scan.lock",
    "stop.flag",
    "pause.flag"
];

// -------- CLEAN PREVIOUS SCAN --------
foreach ($files as $file) {
    $path = $basePath . $file;
    if (file_exists($path)) {
        unlink($path);
    }
}

// -------- CREATE LOCK (SCAN RUNNING) --------
file_put_contents($basePath . "scan.lock", "running");

// -------- BUILD JAVA COMMAND --------
// start /B is IMPORTANT (runs async)
$command = "start /B java -jar \"$jarPath\" $website$demoFlag";

// Run WITHOUT waiting
pclose(popen($command, "r"));

// -------- RETURN TO DASHBOARD --------
header("Location: index.php");
exit;
?>
