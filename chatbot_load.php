<?php
require_once("session_config.php");
require_once("db.php");

$id=intval($_GET['id']);
$user=$_SESSION['user_id'];

$res=$conn->query("
SELECT role,message FROM chatbot_messages
WHERE conversation_id=$id AND user_id=$user
ORDER BY id ASC
");

$data=[];
while($row=$res->fetch_assoc()){
$data[]=$row;
}

echo json_encode($data);
?>