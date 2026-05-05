```php id="modern-reports"
<?php
require '../includes/auth.php';
require '../db.php';

$collection = $db->reports;

if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = new MongoDB\BSON\ObjectId($_GET['id']);

    if ($_GET['action'] == 'approve') {
        $collection->updateOne(['_id' => $id], ['$set' => ['status' => 'approved']]);
    }

    if ($_GET['action'] == 'reject') {
        $collection->updateOne(['_id' => $id], ['$set' => ['status' => 'rejected']]);
    }

    header("Location: reports.php");
    exit;
}

$reports = $collection->find([], ['sort' => ['created_at' => -1]]);

/* COUNTS */
$total = $collection->countDocuments();
$pending = $collection->countDocuments(['status'=>'pending']);
$approved = $collection->countDocuments(['status'=>'approved']);
?>

<!DOCTYPE html>
<html>
<head>
<title>Reports</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<style>

/* GLOBAL */
body{
    margin:0;
    font-family:'Segoe UI';
    background:#f4f6f5;
}

/* MAIN */
.main{
    margin-left:260px;
    padding:25px;
}

/* HEADER */
.header{
    margin-bottom:20px;
}

/* STATS */
.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:15px;
    margin-bottom:20px;
}

.stat{
    background:white;
    padding:15px;
    border-radius:14px;
}

/* REPORT CARD */
.report{
    display:flex;
    gap:15px;
    background:white;
    padding:18px;
    border-radius:16px;
    margin-bottom:15px;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
}

.report img{
    width:200px;
    height:130px;
    object-fit:cover;
    border-radius:10px;
}

/* DETAILS */
.details h3{
    margin:0;
    color:#2d6a4f;
}

.meta{
    font-size:13px;
    color:#666;
}

/* BADGES */
.badge{
    padding:5px 10px;
    border-radius:20px;
    font-size:12px;
}

.pending{background:#fff3cd;color:#856404;}
.approved{background:#d4edda;color:#155724;}
.rejected{background:#f8d7da;color:#721c24;}

.domestic{background:#e6f4ea;color:#2d6a4f;}
.wildlife{background:#fdebd0;color:#e67e22;}

/* MAP */
.map{
    width:200px;
    height:120px;
    border-radius:10px;
}

/* ACTIONS */
.actions{
    display:flex;
    flex-direction:column;
    gap:8px;
}

.btn{
    padding:8px;
    border-radius:6px;
    color:white;
    text-decoration:none;
    text-align:center;
}

.approve{background:#2d6a4f;}
.reject{background:#d00000;}

</style>
</head>

<body>

<div class="main">

<div class="header">
<h2>📋 Reports Dashboard</h2>
<p>Manage and review submitted reports</p>
</div>

<!-- STATS -->
<div class="stats">
<div class="stat">Total Reports<br><strong><?= $total ?></strong></div>
<div class="stat">Pending<br><strong><?= $pending ?></strong></div>
<div class="stat">Approved<br><strong><?= $approved ?></strong></div>
</div>

<!-- REPORTS -->
<?php foreach ($reports as $report): ?>

<div class="report">

<img src="<?= $report['image'] ?>" onclick="openImage('<?= $report['image'] ?>')">

<div class="details">
<h3><?= $report['animal'] ?></h3>

<div class="meta">
<?= $report['name'] ?> • <?= $report['contact'] ?>
</div>

<p><?= $report['description'] ?></p>

<span class="badge <?= $report['status'] ?>">
<?= strtoupper($report['status']) ?>
</span>

<span class="badge <?= ($report['animal_category'] ?? 'domestic') ?>">
<?= ucfirst($report['animal_category'] ?? 'Domestic') ?>
</span>

</div>

<div class="map"
onclick="openMap(<?= $report['latitude'] ?>, <?= $report['longitude'] ?>)">
</div>

<div class="actions">
<a href="?action=approve&id=<?= $report['_id'] ?>" class="btn approve">Approve</a>
<a href="?action=reject&id=<?= $report['_id'] ?>" class="btn reject">Reject</a>
</div>

</div>

<?php endforeach; ?>

</div>

<script>
function openImage(src){
    alert("Image preview: " + src);
}

function openMap(lat,lng){
    alert("Map location: "+lat+","+lng);
}
</script>

</body>
</html>
```
