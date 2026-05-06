<?php
require '../includes/auth.php';
require '../db.php';

/* ===== ROLE FILTER ===== */
function getRoleFilter() {
    if (!isset($_SESSION['role'])) return [];

    if ($_SESSION['role'] === 'domestic_admin') {
        return ['animal_category' => 'domestic'];
    }

    return [];
}

$collection = $db->reports;

/* ===== ACTIONS ===== */
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = new MongoDB\BSON\ObjectId($_GET['id']);

    if ($_GET['action'] == 'approve') {
        $collection->updateOne(['_id'=>$id],['$set'=>['status'=>'approved']]);
    }

    if ($_GET['action'] == 'reject') {
        $collection->updateOne(['_id'=>$id],['$set'=>['status'=>'rejected']]);
    }

    header("Location: reports.php");
    exit;
}

/* ===== FILTERS ===== */
$filter = getRoleFilter();

if (!empty($_GET['search'])) {
    $filter['animal'] = ['$regex'=>$_GET['search'], '$options'=>'i'];
}

if (!empty($_GET['status'])) {
    $filter['status'] = $_GET['status'];
}

if (!empty($_GET['type'])) {
    $filter['animal_category'] = $_GET['type'];
}

/* ===== DATA ===== */
$reports = $collection->find($filter, ['sort'=>['created_at'=>-1]]);

/* ===== COUNTS ===== */
$total = $db->reports->countDocuments(getRoleFilter());

$pending = $db->reports->countDocuments(
    array_merge(getRoleFilter(), ['status'=>'pending'])
);

$approved = $db->reports->countDocuments(
    array_merge(getRoleFilter(), ['status'=>'approved'])
);

$rejected = $db->reports->countDocuments(
    array_merge(getRoleFilter(), ['status'=>'rejected'])
);
?>

<!DOCTYPE html>
<html>
<head>
<title>Reports Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>
.logo{
font-size:34px;
font-weight:bold;
margin-bottom:10px;
}

.logo small{
display:block;
font-size:14px;
font-weight:normal;
margin-top:5px;
}

/* SECTION TITLE */
.section-title{
font-size:12px;
opacity:.7;
margin:25px 0 10px;
}

/* SIDEBAR */
.sidebar{
width:260px;
height:100vh;
position:fixed;
left:0;
top:0;
background:linear-gradient(180deg,#1b4332,#2d6a4f);
padding:20px;
color:white;
overflow:auto;
}

.sidebar a{
display:flex;
align-items:center;
gap:12px;
padding:14px;
margin-bottom:8px;
border-radius:14px;
text-decoration:none;
color:white;
transition:.2s;
}

.sidebar a:hover{
background:rgba(255,255,255,.1);
}

.sidebar a.active{
background:rgba(255,255,255,.2);
}

/* ROLE CARD */
.role-card{
background:rgba(255,255,255,.12);
padding:15px;
border-radius:16px;
margin-top:10px;
}

.role-card small{
opacity:.8;
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
<a href="history.php">Activity Logs</a>
<a href="events.php">Events</a>
<br>
<a href="logout.php">Logout</a>
</div>

<!-- MAIN -->
<div class="main">

<h1>Reports Dashboard</h1>
<p>Manage and review submitted reports</p>

<!-- FILTER -->
<form method="GET" style="margin-bottom:20px;display:flex;gap:10px;flex-wrap:wrap;">
<input type="text" name="search" placeholder="Search animal..." value="<?= $_GET['search'] ?? '' ?>">
<select name="status">
<option value="">All Status</option>
<option value="pending">Pending</option>
<option value="approved">Approved</option>
<option value="rejected">Rejected</option>
</select>
<select name="type">
<option value="">All Type</option>
<option value="domestic">Domestic</option>
<option value="wildlife">Wildlife</option>
</select>
<button>Filter</button>
</form>

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

<?php
$count = 0;
foreach ($reports as $report):
$count++;
?>

<div class="card" style="margin-bottom:15px;">

<img src="<?= $report['image'] ?? '' ?>">

<h3><?= $report['animal'] ?></h3>

<p class="meta">
<?= $report['name'] ?> • <?= $report['contact'] ?>
</p>

<p><?= substr($report['description'],0,100) ?>...</p>

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

<?php if ($count == 0): ?>
<div class="card">No reports found.</div>
<?php endif; ?>

</div>

<!-- RIGHT -->
<div>

<div class="card">
<h3>Quick Insights</h3>
<p>Pending: <?= $pending ?></p>
<p>Approved: <?= $approved ?></p>
<p>Rejected: <?= $rejected ?></p>
<p>Total: <?= $total ?></p>
</div>

<br>

<div class="card">
<h3>Case Tagging</h3>
<span class="badge domestic">Domestic</span><br><br>
<span class="badge wildlife">Wildlife</span>
</div>

</div>

</div>

</div>

</body>
</html>
```
