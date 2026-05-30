<div id="sidebar" class="sidebar">
    <div class="logo"><a href="/WebCheck360-backup/WebCheck360/public/index.php">🔐WebCheck360 </a></div>

    <?php if ($_SESSION['role'] === 'admin'): ?>
    <a href="/WebCheck360-backup/WebCheck360/public/history/login_history.php">📋 Login Activity</a>
<?php endif; ?>

    <a href="/WebCheck360-backup/WebCheck360/public/index.php">🏠 Dashboard</a>
    <a href="/WebCheck360-backup/WebCheck360/public/scanner.php">🔍 Run Scan</a>
    <a href="/WebCheck360-backup/WebCheck360/public/history/scan_history.php">📊 Scan History</a>
    <a href="/WebCheck360-backup/WebCheck360/public/report.php">📄 Last Report</a>
    <!-- <a href="/WebCheck360-backup/WebCheck360/modules/reports/generate_pdf.php">⬇ Export PDF</a> -->
    <!-- <a href="/WebCheck360-backup/WebCheck360/modules/website_scanner/reset_scan.php">♻ Reset</a> -->
    <a href="/WebCheck360-backup/WebCheck360/public/speed_test.php">⚡Speed Test</a>
    <a href="/WebCheck360-backup/WebCheck360/public/history/speed_history.php">📈 Speed History</a>
    <a href="/WebCheck360-backup/WebCheck360/public/analysis.php">🤖 Risk Analyzer</a>
    <a href="/WebCheck360-backup/WebCheck360/public/history/risk_history.php">📊 Risk Monitoring</a>
    <a href="/WebCheck360-backup/WebCheck360/public/chatbot.php">💬 Assistant</a>
</div>
