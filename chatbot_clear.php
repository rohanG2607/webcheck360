<?php
require_once("session_config.php");
require_once("db.php");

$user=$_SESSION['user_id'];
$conn->query("DELETE FROM chatbot_messages WHERE user_id=$user");