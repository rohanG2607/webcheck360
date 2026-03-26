<?php
require_once("session_config.php");
require_once("db.php");

$user=$_SESSION['user_id'];

$stmt=$conn->prepare("INSERT INTO chatbot_conversations(user_id,title) VALUES(?, 'New Chat')");
$stmt->bind_param("i",$user);
$stmt->execute();

echo $stmt->insert_id;
?>