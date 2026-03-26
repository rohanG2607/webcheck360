<?php

$basePath  = "D:/XAMPP/htdocs/webcheck360/";

$stopFile  = $basePath . "stop.flag";
$pauseFile = $basePath . "pause.flag";
$lockFile  = $basePath . "scan.lock";

/* ---------- IF NO ACTIVE SCAN ---------- */
if (!file_exists($lockFile)) {
    echo "No active scan.";
    exit;
}

/* ---------- SIGNAL JAVA TO STOP ---------- */
file_put_contents($stopFile, "STOP");

/* ---------- REMOVE PAUSE (VERY IMPORTANT) ---------- */
if (file_exists($pauseFile)) {
    unlink($pauseFile);
}

/*
Give crawler a moment to break its loop
prevents corrupted JSON writes
*/
sleep(1);

/* ---------- REMOVE LOCK ---------- */
if (file_exists($lockFile)) {
    unlink($lockFile);
}

echo "Scan stopped.";
?>