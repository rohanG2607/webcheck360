<?php
$conn = new mysqli("localhost", "root", "", "webcheck360");

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}
?>
