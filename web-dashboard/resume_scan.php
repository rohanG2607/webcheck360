<?php
$pauseFile = "D:/XAMPP/htdocs/webcheck360/pause.scan";
if (file_exists($pauseFile)) unlink($pauseFile);
echo "Resumed";
?>
