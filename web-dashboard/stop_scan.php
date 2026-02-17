<?php
$stopFile = "D:/XAMPP/htdocs/webcheck360/stop.scan";
file_put_contents($stopFile, "STOP");

// remove pause if exists
$pauseFile = "D:/XAMPP/htdocs/webcheck360/pause.scan";
if (file_exists($pauseFile)) unlink($pauseFile);

echo "Stopping scan...";
?>
