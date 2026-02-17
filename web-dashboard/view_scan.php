<?php
$conn = new mysqli("localhost", "root", "", "webcheck360");

if ($conn->connect_error) {
    die("Database connection failed");
}

if (!isset($_GET['id'])) {
    die("Invalid Scan ID");
}

$id = intval($_GET['id']);

/* ------------------------------
   FETCH SCAN SUMMARY
--------------------------------*/
$result = $conn->query("SELECT * FROM scans WHERE id = $id");

if (!$result || $result->num_rows === 0) {
    die("Scan not found");
}

$scan = $result->fetch_assoc();

/* ------------------------------
   FETCH ALL LINKS FOR THIS SCAN
--------------------------------*/
$links = $conn->query("SELECT url, status_code FROM scan_links WHERE scan_id = $id ORDER BY id ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Scan Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 40px;
            background: #f5f6fa;
        }
        h2 { margin-bottom: 10px; }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            font-size: 14px;
        }

        th {
            background: #2c3e50;
            color: white;
        }

        tr:nth-child(even) {
            background: #f2f2f2;
        }

        .broken { color: red; font-weight: bold; }
        .suspect { color: orange; font-weight: bold; }
        .valid { color: green; }

        .back-btn {
            display: inline-block;
            margin: 15px 0;
            padding: 8px 14px;
            background: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
        }

        .summary {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 6px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>

<div class="summary">
    <h2>Scan Details</h2>

    <b>Website:</b>
    <?= htmlspecialchars($scan['website']) ?><br><br>

    <b>Scan Time:</b>
    <?= htmlspecialchars($scan['scan_time'] ?? 'N/A') ?><br><br>

    <b>Total Links:</b> <?= $scan['total_links'] ?><br>
    <b>Broken Links:</b> <?= $scan['broken_links'] ?><br>
    <b>Suspect Links:</b> <?= $scan['suspect_links'] ?><br>
</div>

<a class="back-btn" href="scan_history.php">← Back to Scan History</a>

<h3>Links Checked</h3>

<table>
<tr>
    <th>URL</th>
    <th>Status Code</th>
    <th>Category</th>
</tr>

<?php if ($links && $links->num_rows > 0): ?>
    <?php while ($row = $links->fetch_assoc()):

        $status = (int)$row['status_code'];

        // Determine category dynamically
        if ($status >= 400) {
            $category = "Broken";
            $class = "broken";
        } elseif ($status == 401 || $status == 403) {
            $category = "Suspect";
            $class = "suspect";
        } else {
            $category = "Valid";
            $class = "valid";
        }
    ?>
        <tr>
            <td><?= htmlspecialchars($row['url']) ?></td>
            <td><?= $status ?></td>
            <td class="<?= $class ?>"><?= $category ?></td>
        </tr>
    <?php endwhile; ?>
<?php else: ?>
<tr>
    <td colspan="3">No links stored for this scan.</td>
</tr>
<?php endif; ?>

</table>

</body>
</html>

<?php $conn->close(); ?>
