<?php
require_once(__DIR__."/../../app/helpers/session_config.php");
require_once(__DIR__."/../../app/helpers/db.php");
require_once(__DIR__."/../../app/services/ai_engine.php");

header('Content-Type: application/json');

$user_id = $_SESSION['user_id'] ?? 0;
$message = $_POST['message'] ?? '';
$mode    = $_POST['mode'] ?? 'offline';
$conversation_id = intval($_POST['conversation_id'] ?? 0);

if (!$user_id || !$conversation_id || !$message) {
    echo json_encode(["reply"=>"Invalid request."]);
    exit;
}

/* ===============================
   BUILD PROJECT CONTEXT
=============================== */
function buildProjectContext($conn){

$context = "";

/* Latest Scan */
$res = $conn->query("
SELECT website,total_links,broken_links,suspect_links,health_score
FROM scans ORDER BY id DESC LIMIT 1
");

if ($res && $row = $res->fetch_assoc()){
$context .= "Latest Scan:\n";
$context .= "Website: ".$row['website']."\n";
$context .= "Health Score: ".$row['health_score']."%\n";
$context .= "Total Links: ".$row['total_links']."\n";
$context .= "Broken Links: ".$row['broken_links']."\n";
$context .= "Suspect Links: ".$row['suspect_links']."\n\n";
}

/* Speed Data */
$res = $conn->query("
SELECT load_time, performance_score, page_size
FROM speed_tests ORDER BY id DESC LIMIT 1
");

if ($res && $row = $res->fetch_assoc()){
$context .= "Performance Metrics:\n";
$context .= "Load Time: ".$row['load_time']."s\n";
$context .= "Performance Score: ".$row['performance_score']."\n";
$context .= "Page Size: ".$row['page_size']." KB\n\n";
}

/* Risk Analysis */
$res = $conn->query("
SELECT risk_level, reason
FROM ai_analysis_history ORDER BY id DESC LIMIT 1
");

if ($res && $row = $res->fetch_assoc()){
$context .= "Risk Analysis:\n";
$context .= "Risk Level: ".$row['risk_level']."\n";
$context .= "Reason: ".$row['reason']."\n\n";
}

return $context ?: "No analysis data available yet.";
}

/* ===============================
   SAVE MESSAGE
=============================== */
function saveMessage($conn,$user,$conv,$role,$msg){

$stmt = $conn->prepare("
INSERT INTO chatbot_messages(user_id,conversation_id,role,message)
VALUES(?,?,?,?)
");
$stmt->bind_param("iiss",$user,$conv,$role,$msg);
$stmt->execute();

/* Auto rename */
if ($role === "user"){
$stmt = $conn->prepare("
UPDATE chatbot_conversations
SET title = LEFT(?,40)
WHERE id=? AND title='New Chat'
");
$stmt->bind_param("si",$msg,$conv);
$stmt->execute();
}
}

/* ===============================
   OFFLINE MODE
=============================== */
function offlineReply($msg,$context){

$msg = strtolower($msg);

if (strpos($msg,"improve")!==false){
return "Recommended Improvements:\n".
"- Fix broken links\n".
"- Optimize load speed\n".
"- Reduce page size\n".
"- Enable caching\n\n".$context;
}

if (strpos($msg,"risk")!==false){
return "Risk Explanation:\n".$context;
}

if (strpos($msg,"performance")!==false || strpos($msg,"speed")!==false){
return "Performance Breakdown:\n".$context;
}

return "Here is your latest website analysis:\n".$context;
}

/* ===============================
   EXECUTION
=============================== */
try{

$context = buildProjectContext($conn);

/* Save user message */
saveMessage($conn,$user_id,$conversation_id,"user",$message);

/* Generate reply */
if ($mode === "online") {

    $systemPrompt = "You are WebCheck360 Assistant. Analyze the provided website data and give structured, actionable insights.";

    $reply = generateAIResponse($systemPrompt,$context,$message);

} else {

    $reply = offlineReply($message,$context);
}

/* Save bot message */
saveMessage($conn,$user_id,$conversation_id,"bot",$reply);

echo json_encode(["reply"=>$reply]);

}catch(Throwable $e){

error_log("Chatbot Error: ".$e->getMessage());
echo json_encode(["reply"=>"Assistant temporarily unavailable."]);

}
?>