<?php
require_once(__DIR__."/../app/helpers/session_config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>WebCheck360 Assistant</title>

<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/theme.css">
<link rel="stylesheet" href="assets/css/chatbot.css">
</head>

<body>

<div class="wrapper">
<?php include __DIR__.'/../app/views/partials/sidebar.php'; ?>

<div class="main">
<?php include __DIR__.'/../app/views/partials/topbar.php'; ?>

<h2>💬 WebCheck360 Assistant</h2>

<div class="chat-layout">

    <!-- LEFT CHAT LIST -->
    <div class="chat-sidebar">
        <button class="new-chat-btn" onclick="newChat()">+ New Chat</button>
        <div id="chatList"></div>
    </div>

    <!-- MAIN CHAT -->
    <div class="chat-main">

        <!-- MODE TOGGLE -->
        <div class="chat-mode">
            <label class="switch">
                <input type="checkbox" id="modeToggle">
                <span class="slider"></span>
            </label>
            <span id="modeLabel">Offline Mode</span>
        </div>

        <!-- Suggested Questions -->
        <div class="suggestions">
            <button onclick="askSuggestion('Give me summary of latest scan')">Summary</button>
            <button onclick="askSuggestion('How can I improve performance?')">Improve</button>
            <button onclick="askSuggestion('Explain my risk level')">Risk</button>
        </div>

        <!-- CHAT BOX -->
        <div class="chat-container">
            <div id="chat-box"></div>

            <div class="chat-input">
                <input type="text" id="userInput"
                       placeholder="Ask about your scan, speed, or risk...">
                <button onclick="sendMessage()">Send</button>
            </div>
        </div>

    </div>

</div>

</div>
</div>

<script>
let onlineMode = false;
let currentConversation = null;

/* =============================
   AUTO CREATE CHAT ON PAGE LOAD
============================= */
window.addEventListener("load", () => {
fetch("/WebCheck360-backup/WebCheck360/modules/chatbot/chatbot_new.php")
.then(r=>r.text())
.then(id=>{
currentConversation = id;
loadChats();
openChat(id);
});
});

/* =============================
   LOAD CONVERSATIONS
============================= */
function loadChats(){
fetch("/WebCheck360-backup/WebCheck360/modules/chatbot/chatbot_list.php")
.then(r=>r.json())
.then(data=>{
let html="";
data.forEach(c=>{
html += `
<div class="chat-item">
<span onclick="openChat(${c.id})">${c.title}</span>
<button class="delete-btn"
onclick="event.stopPropagation();deleteChat(${c.id})">×</button>
</div>`;
});
document.getElementById("chatList").innerHTML = html;
});
}

/* =============================
   CREATE NEW CHAT
============================= */
function newChat(){
fetch("/WebCheck360-backup/WebCheck360/modules/chatbot/chatbot_new.php")
.then(r=>r.text())
.then(id=>{
currentConversation = id;
document.getElementById("chat-box").innerHTML="";
loadChats();
});
}

/* =============================
   OPEN CHAT HISTORY
============================= */
function openChat(id){
currentConversation = id;
document.getElementById("chat-box").innerHTML="";

fetch("/WebCheck360-backup/WebCheck360/modules/chatbot/chatbot_history.php?conversation_id="+id)
.then(r=>r.json())
.then(data=>{
data.forEach(m=>appendMessage(m.role,m.message,false));
});
}

/* =============================
   DELETE CHAT
============================= */
function deleteChat(id){
fetch("/WebCheck360-backup/WebCheck360/modules/chatbot/chatbot_delete.php",{
method:"POST",
headers:{'Content-Type':'application/x-www-form-urlencoded'},
body:"id="+id
}).then(()=>loadChats());
}

/* =============================
   MODE TOGGLE
============================= */
document.getElementById("modeToggle").addEventListener("change",function(){
onlineMode=this.checked;
document.getElementById("modeLabel").innerText=
onlineMode?"Online AI Mode":"Offline Mode";
});

/* =============================
   SUGGESTION CLICK
============================= */
function askSuggestion(text){
document.getElementById("userInput").value=text;
sendMessage();
}

/* =============================
   SEND MESSAGE
============================= */
function sendMessage(){

let input=document.getElementById("userInput");
let msg=input.value.trim();
if(!msg)return;

appendMessage("user",msg,true);

fetch("/WebCheck360-backup/WebCheck360/modules/chatbot/chatbot_api.php",{
method:"POST",
headers:{'Content-Type':'application/x-www-form-urlencoded'},
body:"message="+encodeURIComponent(msg)
+"&conversation_id="+currentConversation
+"&mode="+(onlineMode?"online":"offline")
})
.then(r=>r.json())
.then(data=>{
typingEffect(data.reply || "No response");
})
.catch(()=>{
typingEffect("Assistant unavailable.");
});

input.value="";
}

/* =============================
   TYPING EFFECT
============================= */
function typingEffect(text){
let box=document.getElementById("chat-box");
let div=document.createElement("div");
div.className="msg bot";
box.appendChild(div);

let i=0;
let t=setInterval(()=>{
div.innerText=text.substring(0,i++);
box.scrollTop=box.scrollHeight;
if(i>text.length)clearInterval(t);
},12);
}

/* =============================
   APPEND MESSAGE
============================= */
function appendMessage(type,text,save){
let box=document.getElementById("chat-box");
let div=document.createElement("div");
div.className="msg "+type;
div.innerText=text;
box.appendChild(div);
box.scrollTop=box.scrollHeight;

if(save){
fetch("/WebCheck360-backup/WebCheck360/modules/chatbot/chatbot_save.php",{
method:"POST",
headers:{'Content-Type':'application/x-www-form-urlencoded'},
body:"role="+type
+"&conversation_id="+currentConversation
+"&message="+encodeURIComponent(text)
});
}
}
</script>

</body>
</html>