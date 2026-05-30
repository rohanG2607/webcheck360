<?php
require_once(__DIR__."/../../app/helpers/session_config.php");
require_once(__DIR__."/../../app/helpers/db.php");

$user=$_SESSION['user_id'];
$conn->query("DELETE FROM chatbot_messages WHERE user_id=$user");