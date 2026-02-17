<?php
/* ---------------------------------------
   RESET THE SCANNER STATE COMPLETELY
----------------------------------------*/

// Absolute path (must match Java BASE path)
$base = "D:/XAMPP/htdocs/webcheck360/";

// Files created during scan
$files = [
    "progress.json",
    "report.json",
    "links.json",
    "scan.lock",
    "stop.scan",
    "pause.scan"
];

// Delete each file safely
foreach ($files as $file) {
    $path = $base . $file;

    if (file_exists($path)) {
        unlink($path);
    }
}

/* ---------------------------------------
   Redirect back to Dashboard (Fresh State)
----------------------------------------*/
header("Location: index.php?reset=success");
exit;
?>
