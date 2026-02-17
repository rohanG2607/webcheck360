<?php
// Must match BASE_PATH from App.java
$pauseFile = "D:/XAMPP/htdocs/webcheck360/pause.flag";

// Create pause flag
file_put_contents($pauseFile, "PAUSE");

echo "Scan paused.";
?>
