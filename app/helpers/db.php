<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "webcheck360";

mysqli_report(MYSQLI_REPORT_OFF); // disable auto error display

$conn = @new mysqli($host, $user, $pass, $db);

/* If connection fails → log error instead of showing */
if ($conn->connect_error) {

    error_log("DB Connection Failed: " . $conn->connect_error);

    // return null connection safely
    $conn = null;
}
?>