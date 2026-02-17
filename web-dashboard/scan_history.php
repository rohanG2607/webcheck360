<?php
require("db.php");

$result = $conn->query("SELECT * FROM scans ORDER BY scan_time DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>WebCheck360 - Scan History</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: center;
        }
        th {
            background: #f4f4f4;
        }
        a {
            text-decoration: none;
        }
    </style>
</head>
<body>

<h2>Scan History</h2>
<br>
<button type="submit"><a href="index.php">← Back to Dashboard</a></button>

<table>
    <tr>
        <th>ID</th>
        <th>Website</th>
        <th>Total Links</th>
        <th>Broken</th>
        <th>Suspect</th>
        <th>Date</th>
        <th>Action</th>
    </tr>

    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?= $row['id'] ?></td>
        <td><?= $row['website'] ?></td>
        <td><?= $row['total_links'] ?></td>
        <td style="color:red;"><?= $row['broken_links'] ?></td>
        <td style="color:orange;"><?= $row['suspect_links'] ?></td>
        <td><?= $row['scan_time'] ?></td>
        <td>
            <a href="view_scan.php?id=<?= $row['id'] ?>">View Details</a>
        </td>
    </tr>
    <?php } ?>

</table>


</body>
</html>
