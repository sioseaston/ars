<?php
require '../includes/auth.php';
require '../db.php';

$collection = $db->reports;

/* ACTIONS */
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
$rejected = $collection->countDocuments(['status'=>'rejected']);
?>

<!DOCTYPE html>
<html>
<head>
<title>Reports Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>

/* ===== GLOBAL ===== */
*{box-sizing:border-box}
body{
    margin:0;
    font-family:'Segoe UI';
    background:#f4f6f5;
}

/* ===== SIDEBAR ===== */
.sidebar{
    width:260px;
    height:100vh;
    position:fixed;
    background:linear-gradient(180deg,#1b4332,#2d6a4f);
    color:white;
    padding:20px;
}

.sidebar a{
    display:block;
    padding:10px;
    margin:5px 0;
    border-radius:10px;
    color:white;
    text-decoration:none;
}
.sidebar a.active{background:rgba(255,255,255,.25)}

/* ===== MAIN ===== */
.main{
    margin-left:260px;
    padding:25px;
}

/* ===== STATS ===== */
.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:20px;
}

.card{
    background:white;
    padding:18px;
    border-radius:16px;
    box-shadow:0 5px 15px rgba(0,0,0,.05);
}

/* ===== GRID ===== */
.grid{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:20px;
    margin-top:20px;
}

/* ===== META ===== */
.meta{
    font-size:13px;
    color:#666;
}

/* ===== BADGES ===== */
.badge{
    padding:5px 10px;
    border-radius:12px;
    font-size:12px;
}

.pending{background:#fff3cd;color:#856404;}
.approved{background:#d4edda;color:#155724;}
.rejected{background:#f8d7da;color:#721c24;}

.domestic{background:#e6f4ea;color:#2d6a4f;}
.wildlife{background:#fdebd0;color:#e67e22;}

/* ===== BUTTONS ===== */
.btn{
    padding:8px 12px;
    border-radius:8px;
    color:white;
    text-decoration:none;
    display:inline-block;
    margin-top:5px;
}

.approve{background:#2d6a4f;}
.reject{background:#d00000;}

img{
    width:100%;
    border-radius:10px;
    margin-bottom:10px;
}

/* ===== MOBILE ===== */
@media(max-width:768px){
    .sidebar{display:none;}
    .main{margin-left:0;}
    .grid{grid-template-columns:1fr;}
}

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
<h2>🐾 ARSS</h2>

<a href="dashboard.php">Dashboard</a>
<a href="reports.php" class="active">Reports</a>
<a href="surrenders.php">Surrenders</a>
<a href="adoptions.php">Adoptions</a>
<a href="history.php">Activity Logs</a>
<a href="events.php">Events</a>

<br>
<a href="logout.php">Logout</a>
</div>

<!-- MAIN -->
<div class="main">

<h1>Reports Dashboard</h1>
<p>Manage and review submitted reports</p>

<!-- STATS -->
<div class="stats">

<div class="card">
<h4>Total Reports</h4>
<h2><?= $total ?></h2>
</div>

<div class="card">
<h4>Pending</h4>
<h2><?= $pending ?></h2>
</div>

<div class="card">
<h4>Approved</h4>
<h2><?= $approved ?></h2>
</div>

<div class="card">
<h4>Rejected</h4>
<h2><?= $rejected ?></h2>
</div>

</div>

<!-- GRID -->
<div class="grid">

<!-- LEFT -->
<div>

<?php foreach ($reports as $report): ?>

<div class="card" style="margin-bottom:15px;">

<img src="<?= $report['image'] ?>">

<h3><?= $report['animal'] ?></h3>

<p class="meta">
<?= $report['name'] ?> • <?= $report['contact'] ?>
</p>

<p><?= $report['description'] ?></p>

<span class="badge <?= $report['status'] ?>">
<?= strtoupper($report['status']) ?>
</span>

<span class="badge <?= ($report['animal_category'] ?? 'domestic') ?>">
<?= ucfirst($report['animal_category'] ?? 'Domestic') ?>
</span>

<br><br>

<a href="?action=approve&id=<?= $report['_id'] ?>" class="btn approve">Approve</a>
<a href="?action=reject&id=<?= $report['_id'] ?>" class="btn reject">Reject</a>

</div>

<?php endforeach; ?>

</div>

<!-- RIGHT -->
<div>

<div class="card">
<h3>Quick Insights</h3>
<p>Pending Reports: <?= $pending ?></p>
<p>Approved Reports: <?= $approved ?></p>
<p>Rejected Reports: <?= $rejected ?></p>
<p>Total Reports: <?= $total ?></p>
</div>

<br>

<div class="card">
<h3>Case Tagging</h3>
<span class="badge domestic">Domestic Cases</span><br><br>
<span class="badge wildlife">Wildlife Cases</span>
</div>

<br>

<div class="card">
<h3>Escalation System</h3>
<p>Reassign misclassified reports</p>
<button>Go to Reassignment</button>
</div>

</div>

</div>

</div>

</body>
</html>
