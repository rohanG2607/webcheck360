<?php
require_once(__DIR__."/../../app/helpers/session_config.php");
require_once(__DIR__."/../../app/helpers/db.php");

$user=$_SESSION['user_id'];
$conv=$_GET['conversation_id'];

$res=$conn->query("SELECT role,message FROM chatbot_messages 
WHERE user_id=$user AND conversation_id=$conv ORDER BY id ASC");

$data=[];
while($r=$res->fetch_assoc()) $data[]=$r;

echo json_encode($data);