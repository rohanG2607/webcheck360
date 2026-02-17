<!DOCTYPE html>
<html>
<head>
    <title>WebCheck360 Dashboard</title>

    <style>
        body {
            font-family: Arial;
            margin: 40px;
            background: #f5f6fa;
        }

        h2 { margin-bottom: 10px; }

        input[type=text] {
            padding: 8px;
            width: 380px;
        }

        button {
            padding: 8px 14px;
            margin: 5px;
            cursor: pointer;
            border: none;
            border-radius: 4px;
        }

        .start { background:#3498db; color:white; }
        .stop { background:#e74c3c; color:white; }
        .pause { background:#f39c12; color:white; }
        .resume { background:#27ae60; color:white; }
        .reset { background:#7f8c8d; color:white; }
        .history { background:#2ecc71; color:white; }

        #progressContainer {
            width: 100%;
            background: #ddd;
            border-radius: 20px;
            margin-top: 20px;
        }

        #progressBar {
            width: 0%;
            height: 22px;
            background: #4caf50;
            border-radius: 20px;
            transition: width 0.5s;
        }

        #currentUrl {
            font-size: 12px;
            margin-top: 5px;
            word-break: break-all;
        }
    </style>
</head>
<body>

<h2>🌐 WebCheck360 – Website Health Scanner</h2>

<form method="post" action="run_scan.php">
    <input type="text" name="website" placeholder="https://example.com" required>

    <label>
        <input type="checkbox" name="demo"> Demo Mode
    </label>

    <br><br>
    <button class="start" type="submit">Start Scan</button>
</form>

<hr>

<h3>📊 Live Scan Progress</h3>

<div id="progressContainer">
    <div id="progressBar"></div>
</div>

<p id="progressText">Waiting for scan...</p>

<p><b>Current URL:</b></p>
<div id="currentUrl">—</div>

<br>

<button class="stop" onclick="stopScan()">Stop</button>
<button class="pause" onclick="pauseScan()">Pause</button>
<button class="resume" onclick="resumeScan()">Resume</button>
<button class="reset" onclick="resetScan()">Reset</button>
<button class="history" onclick="window.location.href='scan_history.php'">View History</button>

<script>

let scanStarted = false;

/* ------------------------------
   CHECK IF SCAN RUNNING
------------------------------ */
function checkScan() {

    fetch("scan.lock?ts=" + Date.now())
    .then(res => {

        if (res.ok) {
            scanStarted = true;
            fetchProgress();
        }
        else {
            if (scanStarted) {
                // Scan finished → go to report
                window.location.href = "report.php";
            }
        }
    })
    .catch(()=>{});
}

/* ------------------------------
   FETCH LIVE PROGRESS
------------------------------ */
function fetchProgress() {

    fetch("progress.json?ts=" + Date.now())
    .then(r => r.json())
    .then(d => {

        let percent = d.percent || 0;

        document.getElementById("progressBar").style.width = percent + "%";
        document.getElementById("progressText").innerText =
            percent + "% completed (" + d.checked + " checked)";

        document.getElementById("currentUrl").innerText =
            d.currentUrl + " [" + d.status + "]";
    })
    .catch(()=>{});
}

/* ------------------------------
   CONTROL BUTTONS
------------------------------ */

function stopScan() {
    fetch("stop_scan.php");
    document.getElementById("progressText").innerText = "Stopping scan...";
}

function pauseScan() {
    fetch("pause_scan.php");
    document.getElementById("progressText").innerText = "Scan paused";
}

function resumeScan() {
    fetch("resume_scan.php");
    document.getElementById("progressText").innerText = "Resuming scan...";
}

function resetScan() {
    window.location.href = "reset_scan.php";
}

/* ------------------------------
   POLL EVERY SECOND
------------------------------ */
setInterval(checkScan, 1000);

</script>

</body>
</html>
