<?php

$basePath  = "D:/XAMPP/htdocs/webcheck360/";
$pauseFile = $basePath . "pause.flag";
$lockFile  = $basePath . "scan.lock";

/* ---------- CHECK ACTIVE SCAN ---------- */
if (!file_exists($lockFile)) {
    echo "No active scan to pause.";
    exit;
}

/* ---------- PREVENT DUPLICATE PAUSE ---------- */
if (file_exists($pauseFile)) {
    echo "Scan already paused.";
    exit;
}

/* ---------- CREATE PAUSE FLAG ---------- */
file_put_contents($pauseFile, "PAUSE");

echo "Scan paused.";
?>