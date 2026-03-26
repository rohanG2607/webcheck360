<?php
require_once("session_config.php");
require_once("db.php");

$user=$_SESSION['user_id'];

$res=$conn->query("SELECT id,title FROM chatbot_conversations 
WHERE user_id=$user ORDER BY id DESC");

$data=[];
while($row=$res->fetch_assoc()){
$data[]=$row;
}

echo json_encode($data);
?>