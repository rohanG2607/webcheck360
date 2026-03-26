<div id="sidebar" class="sidebar">
    <div class="logo"><a href="index.php">🔐WebCheck360 </a></div>

    <?php if ($_SESSION['role'] === 'admin'): ?>
    <a href="login_history.php">📋 Login Activity</a>
<?php endif; ?>

    <a href="index.php">🏠 Dashboard</a>
    <a href="scanner.php">🔍 Run Scan</a>
    <a href="scan_history.php">📊 Scan History</a>
    <a href="report.php">📄 Last Report</a>
    <!-- <a href="generate_pdf.php">⬇ Export PDF</a> -->
    <!-- <a href="reset_scan.php">♻ Reset</a> -->
    <a href="speed_test.php">⚡Speed Test</a>
    <a href="speed_history.php">📈 Speed History</a>
    <a href="analysis.php">🤖 Risk Analyzer</a>
    <a href="risk_history.php">📊 Risk Monitoring</a>
    <a href="chatbot.php">💬 Assistant</a>
</div>
