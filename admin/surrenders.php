<?php
require '../includes/auth.php';
require '../db.php';

$collection = $db->surrenders;

/* ACTION */
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = new MongoDB\BSON\ObjectId($_GET['id']);

    if ($_GET['action'] == 'approve') {
        $collection->updateOne(['_id'=>$id],['$set'=>['status'=>'approved']]);
    }

    if ($_GET['action'] == 'reject') {
        $collection->updateOne(['_id'=>$id],['$set'=>['status'=>'rejected']]);
    }

    header("Location: surrenders.php");
    exit;
}

$surrenders = $collection->find([],['sort'=>['created_at'=>-1]]);

$total = $collection->countDocuments();
$pending = $collection->countDocuments(['status'=>'pending']);
?>

<!DOCTYPE html>
<html>
<head>
<title>Surrenders</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body{margin:0;font-family:'Segoe UI';background:#f4f6f5;}
.sidebar{width:260px;height:100vh;position:fixed;background:linear-gradient(#1b4332,#2d6a4f);color:white;padding:20px;}
.sidebar a{display:block;color:white;padding:10px;margin:5px 0;border-radius:10px;text-decoration:none;}
.sidebar a.active{background:rgba(255,255,255,.25);}
.main{margin-left:260px;padding:25px;}
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;}
.card{background:white;padding:18px;border-radius:16px;box-shadow:0 5px 15px rgba(0,0,0,.05);}
.grid{display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-top:20px;}
.badge{padding:5px 10px;border-radius:12px;font-size:12px;}
.pending{background:#fff3cd;color:#856404;}
.approved{background:#d4edda;color:#155724;}
.rejected{background:#f8d7da;color:#721c24;}
.btn{padding:8px 12px;border-radius:8px;color:white;text-decoration:none;}
.approve{background:#2d6a4f;}
.reject{background:#d00000;}
</style>
</head>

<body>

<div class="sidebar">
<h2>🐾 ARSS</h2>
<a href="dashboard.php">Dashboard</a>
<a href="reports.php">Reports</a>
<a href="surrenders.php" class="active">Surrenders</a>
<a href="events.php">Events</a>
<a href="logout.php">Logout</a>
</div>

<div class="main">

<h1>Surrender Dashboard</h1>

<div class="stats">
<div class="card"><h4>Total</h4><h2><?= $total ?></h2></div>
<div class="card"><h4>Pending</h4><h2><?= $pending ?></h2></div>
</div>

<div class="grid">

<div>
<?php foreach($surrenders as $s): ?>
<div class="card" style="margin-bottom:15px;">
<h3><?= $s['animal'] ?></h3>
<p><?= $s['name'] ?></p>
<p><?= $s['reason'] ?></p>

<span class="badge <?= $s['status'] ?>">
<?= strtoupper($s['status']) ?>
</span><br><br>

<a href="?action=approve&id=<?= $s['_id'] ?>" class="btn approve">Approve</a>
<a href="?action=reject&id=<?= $s['_id'] ?>" class="btn reject">Reject</a>
</div>
<?php endforeach; ?>
</div>

<div class="card">
<h3>Insights</h3>
<p>Total: <?= $total ?></p>
<p>Pending: <?= $pending ?></p>
</div>

</div>

</div>
</body>
</html>
