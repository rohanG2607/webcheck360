<?php

$basePath = "D:/XAMPP/htdocs/webcheck360/";
$pauseFile = $basePath . "pause.flag";
$lockFile  = $basePath . "scan.lock";

/* ---------- IF NO ACTIVE SCAN ---------- */
if (!file_exists($lockFile)) {
    echo "No active scan";
    exit;
}

/* ---------- RESUME ---------- */
if (file_exists($pauseFile)) {
    unlink($pauseFile);
}

echo "Scan resumed";
?>