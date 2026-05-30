<?php
require_once(__DIR__."/../../app/helpers/session_config.php");
require_once(__DIR__."/../../app/helpers/db.php");

$user=$_SESSION['user_id'];
$conv=$_POST['conversation_id'];
$role=$_POST['role'];
$msg=$_POST['message'];

$stmt=$conn->prepare("
INSERT INTO chatbot_messages(user_id,conversation_id,role,message)
VALUES(?,?,?,?)
");
$stmt->bind_param("iiss",$user,$conv,$role,$msg);
$stmt->execute();

/* Rename chat using first user message */
if($role=="user"){
$conn->query("
UPDATE chatbot_conversations
SET title = LEFT('".$conn->real_escape_string($msg)."',40)
WHERE id=$conv AND title='New Chat'
");
}
?>