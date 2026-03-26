<?php
require_once("session_config.php");
require_once("db.php");

$id=$_POST['id'];
$user=$_SESSION['user_id'];

$conn->query("DELETE FROM chatbot_conversations WHERE id=$id AND user_id=$user");
$conn->query("DELETE FROM chatbot_messages WHERE conversation_id=$id AND user_id=$user");
?>