<?php
require_once("session_config.php");
require_once("db.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

/* ------------------------------
   FETCH DISTINCT URL LIST (USER BASED)
------------------------------ */
$stmt = $conn->prepare("
    SELECT DISTINCT url 
    FROM speed_tests 
    WHERE user_id = ?
    ORDER BY url ASC
");
$stmt->bind_param("i",$user_id);
$stmt->execute();
$urlList = $stmt->get_result();

/* ------------------------------
   GET SELECTED VALUES
------------------------------ */
$urlA = $_GET['urlA'] ?? '';
$urlB = $_GET['urlB'] ?? '';

/* ------------------------------
   FUNCTION TO FETCH CHART DATA
------------------------------ */
function fetchMetrics($conn,$user_id,$url){

    if(!$url) return [[],[]];

    $stmt = $conn->prepare("
        SELECT tested_at, load_time
        FROM speed_tests
        WHERE user_id=? AND url=?
        ORDER BY tested_at ASC
    ");
    $stmt->bind_param("is",$user_id,$url);
    $stmt->execute();
    $res = $stmt->get_result();

    $dates=[];
    $load=[];

    while($r=$res->fetch_assoc()){
        $dates[] = date("d M H:i",strtotime($r['tested_at']));
        $load[]  = (float)$r['load_time'];
    }

    return [$dates,$load];
}

list($datesA,$loadA)=fetchMetrics($conn,$user_id,$urlA);
list($datesB,$loadB)=fetchMetrics($conn,$user_id,$urlB);

/* ------------------------------
   TABLE DATA (FIXED FOR BOTH)
------------------------------ */
if($urlA && $urlB){

    $stmt = $conn->prepare("
        SELECT * FROM speed_tests
        WHERE user_id=? AND (url=? OR url=?)
        ORDER BY tested_at DESC
        LIMIT 20
    ");
    $stmt->bind_param("iss",$user_id,$urlA,$urlB);

}elseif($urlA){

    $stmt = $conn->prepare("
        SELECT * FROM speed_tests
        WHERE user_id=? AND url=?
        ORDER BY tested_at DESC
        LIMIT 10
    ");
    $stmt->bind_param("is",$user_id,$urlA);

}elseif($urlB){

    $stmt = $conn->prepare("
        SELECT * FROM speed_tests
        WHERE user_id=? AND url=?
        ORDER BY tested_at DESC
        LIMIT 10
    ");
    $stmt->bind_param("is",$user_id,$urlB);

}else{

    $stmt = $conn->prepare("
        SELECT * FROM speed_tests
        WHERE user_id=?
        ORDER BY tested_at DESC
        LIMIT 10
    ");
    $stmt->bind_param("i",$user_id);
}

$stmt->execute();
$tableData = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
<title>Speed Monitoring | WebCheck360</title>

<link rel="stylesheet" href="assets/css/theme.css">
<link rel="stylesheet" href="assets/css/layout.css">
<link rel="stylesheet" href="assets/css/speed.css">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>

<body>

<div class="wrapper">
<?php include 'sidebar.php'; ?>

<div class="main">
<?php include 'topbar.php'; ?>

<h2>📈 Speed Monitoring & Comparison</h2>

<!-- AUTO APPLY FILTER -->
<form method="GET" id="filterForm" class="filter-form">

<label>Website A:</label>
<select name="urlA" onchange="document.getElementById('filterForm').submit()">
<option value="">Select</option>
<?php $urlList->data_seek(0); while ($u=$urlList->fetch_assoc()): ?>
<option value="<?= $u['url'] ?>" <?=($urlA==$u['url'])?'selected':''?>>
<?= htmlspecialchars($u['url']) ?>
</option>
<?php endwhile; ?>
</select>

<label>Website B:</label>
<select name="urlB" onchange="document.getElementById('filterForm').submit()">
<option value="">Select</option>
<?php $urlList->data_seek(0); while ($u=$urlList->fetch_assoc()): ?>
<option value="<?= $u['url'] ?>" <?=($urlB==$u['url'])?'selected':''?>>
<?= htmlspecialchars($u['url']) ?>
</option>
<?php endwhile; ?>
</select>

</form>

<!-- CHART -->
<div class="chart-container">
<canvas id="historyChart"></canvas>
</div>

<h3>Recent Tests</h3>

<table class="history-table">
<tr>
<th>URL</th>
<th>Time</th>
<th>Load Time</th>
<th>TTFB</th>
<th>Page Size</th>
<th>Grade</th>
<th>Score</th>
</tr>

<?php while($row=$tableData->fetch_assoc()): ?>
<tr>
<td><?= htmlspecialchars($row['url']) ?></td>
<td><?= $row['tested_at'] ?></td>
<td><?= $row['load_time'] ?>s</td>
<td><?= $row['ttfb'] ?>s</td>
<td><?= $row['page_size'] ?> KB</td>
<td><?= $row['grade'] ?></td>
<td><?= $row['performance_score'] ?>/100</td>
</tr>
<?php endwhile; ?>
</table>

</div>
</div>

<script>
const ctx=document.getElementById('historyChart');

new Chart(ctx,{
type:'line',
data:{
labels:<?= json_encode(!empty($datesA)?$datesA:$datesB) ?>,
datasets:[

<?php if($urlA): ?>
{
label:'<?= $urlA ?>',
data:<?= json_encode($loadA) ?>,
borderColor:'#38bdf8',
tension:.3
},
<?php endif; ?>

<?php if($urlB): ?>
{
label:'<?= $urlB ?>',
data:<?= json_encode($loadB) ?>,
borderColor:'#f59e0b',
tension:.3
}
<?php endif; ?>

]
},
options:{
plugins:{legend:{labels:{color:'#e2e8f0'}}},
scales:{
x:{ticks:{color:'#94a3b8'}},
y:{ticks:{color:'#94a3b8'}}
}
}
});
</script>

</body>
</html>