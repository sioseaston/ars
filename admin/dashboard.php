<?php
require '../includes/auth.php';
require '../db.php';

/* TOTALS */
$totalReports = $db->reports->countDocuments();
$totalSurrenders = $db->surrenders->countDocuments();
$totalAdoptions = $db->adoptions->countDocuments();
$totalCases = $totalReports + $totalSurrenders + $totalAdoptions;

/* SAMPLE SPLIT (replace later with real field) */
$domesticReports = floor($totalReports * 0.7);
$wildlifeReports = $totalReports - $domesticReports;

$domesticSurrenders = floor($totalSurrenders * 0.7);
$wildlifeSurrenders = $totalSurrenders - $domesticSurrenders;

$domesticAdoptions = floor($totalAdoptions * 0.7);
$wildlifeAdoptions = $totalAdoptions - $domesticAdoptions;

$domesticCases = $domesticReports + $domesticSurrenders + $domesticAdoptions;
$wildlifeCases = $totalCases - $domesticCases;

/* STATUS */
$r_pending = $db->reports->countDocuments(['status'=>'pending']);
$r_approved = $db->reports->countDocuments(['status'=>'approved']);
$r_rejected = $db->reports->countDocuments(['status'=>'rejected']);

$s_pending = $db->surrenders->countDocuments(['status'=>'pending']);
$s_approved = $db->surrenders->countDocuments(['status'=>'approved']);
$s_rejected = $db->surrenders->countDocuments(['status'=>'rejected']);

$a_pending = $db->adoptions->countDocuments(['status'=>'pending']);
$a_approved = $db->adoptions->countDocuments(['status'=>'approved']);
$a_rejected = $db->adoptions->countDocuments(['status'=>'rejected']);
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

/* GLOBAL */
body{
    margin:0;
    font-family:'Segoe UI';
    background:#f4f6f5;
}

/* SIDEBAR */
.sidebar{
    width:260px;
    height:100vh;
    background:linear-gradient(180deg,#1b4332,#2d6a4f);
    color:white;
    position:fixed;
    padding:20px;
}
.sidebar h2{margin:0;}
.sidebar p{font-size:12px;opacity:0.7;}

.sidebar a{
    display:block;
    padding:10px;
    margin:5px 0;
    border-radius:10px;
    color:white;
    text-decoration:none;
}
.sidebar a.active{background:rgba(255,255,255,0.2);}

/* MAIN */
.main{
    margin-left:260px;
    padding:25px;
}

/* TOPBAR */
.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.search{
    padding:10px;
    border-radius:8px;
    border:1px solid #ddd;
    width:250px;
}

.profile{
    display:flex;
    gap:10px;
    align-items:center;
}

/* STATS */
.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(230px,1fr));
    gap:20px;
    margin-top:20px;
}

.card{
    background:white;
    padding:20px;
    border-radius:16px;
}

.card small{color:#777;}

/* GRID */
.grid{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:20px;
    margin-top:20px;
}

/* LOWER GRID */
.grid2{
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:20px;
    margin-top:20px;
}

/* BADGE */
.badge{
    padding:5px 10px;
    border-radius:12px;
    font-size:11px;
}

.domestic{background:#e6f4ea;color:#2d6a4f;}
.wildlife{background:#fdebd0;color:#e67e22;}

/* ESCALATION */
.alert{
    background:#fff3cd;
    padding:15px;
    border-radius:12px;
}

button{
    background:#2d6a4f;
    color:white;
    border:none;
    padding:10px;
    border-radius:8px;
    margin-top:10px;
}

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <h2>🐾 ARSS</h2>
    <p>Animal Rescue Support System</p>

    <hr>

    <a class="active">Dashboard</a>
    <a>Reports</a>
    <a>Surrenders</a>
    <a>Adoptions</a>
    <a>Activity Logs</a>
    <a>Events</a>

    <hr>

    <strong>ROLE & ACCESS</strong>
    <div class="card" style="background:rgba(255,255,255,0.1);margin-top:10px;">
        Domestic Admin<br>
        <small>Access: Domestic Only</small>
    </div>

    <hr>

    <strong>ADMIN TOOLS</strong>
    <a>Case Reassignment</a>
    <a>Analytics</a>
</div>

<!-- MAIN -->
<div class="main">

<!-- TOPBAR -->
<div class="topbar">
    <input class="search" placeholder="Search here...">

    <div class="profile">
        <strong>Admin</strong>
        <small>Domestic Admin</small>
    </div>
</div>

<h1>Admin Dashboard</h1>

<!-- STATS -->
<div class="stats">

<div class="card">
<h4>Total Reports</h4>
<h2><?= $totalReports ?></h2>
<small>Domestic: <?= $domesticReports ?> | Wildlife: <?= $wildlifeReports ?></small>
</div>

<div class="card">
<h4>Total Surrenders</h4>
<h2><?= $totalSurrenders ?></h2>
<small>Domestic: <?= $domesticSurrenders ?> | Wildlife: <?= $wildlifeSurrenders ?></small>
</div>

<div class="card">
<h4>Total Adoptions</h4>
<h2><?= $totalAdoptions ?></h2>
<small>Domestic: <?= $domesticAdoptions ?> | Wildlife: <?= $wildlifeAdoptions ?></small>
</div>

<div class="card">
<h4>Total Cases</h4>
<h2><?= $totalCases ?></h2>
<small>Domestic: <?= $domesticCases ?> | Wildlife: <?= $wildlifeCases ?></small>
</div>

</div>

<!-- CHARTS -->
<div class="grid">

<div class="card">
<h3>Reports (This Month)</h3>
<canvas id="rChart"></canvas>
</div>

<div class="card">
<h3>Case Distribution</h3>
<canvas id="pieChart"></canvas>
</div>

</div>

<!-- LOWER -->
<div class="grid2">

<div class="card">
<h3>Quick Insights</h3>
<p>Pending Reports: <?= $r_pending ?></p>
<p>Pending Surrenders: <?= $s_pending ?></p>
<p>Pending Adoptions: <?= $a_pending ?></p>
<p>Total Active Cases: <?= $totalCases ?></p>
</div>

<div class="card">
<h3>Recent Activity</h3>
<p>New report submitted</p>
<p>New surrender request</p>
<p>New adoption application</p>
</div>

<div class="card">
<h3>Case Tagging Overview</h3>
<span class="badge domestic">Domestic <?= $domesticCases ?></span><br><br>
<span class="badge wildlife">Wildlife <?= $wildlifeCases ?></span>

<div class="alert">
<p>Escalation System</p>
<button>Go to Reassignment</button>
</div>

</div>

</div>

</div>

<script>

/* BAR */
new Chart(rChart,{
type:'bar',
data:{
labels:['Pending','Approved','Rejected'],
datasets:[{
label:'Reports',
data:[<?= $r_pending ?>,<?= $r_approved ?>,<?= $r_rejected ?>],
backgroundColor:['#2d6a4f','#4CAF50','#d00000']
}]
}
});

/* PIE */
new Chart(pieChart,{
type:'doughnut',
data:{
labels:['Domestic','Wildlife'],
datasets:[{
data:[<?= $domesticCases ?>,<?= $wildlifeCases ?>],
backgroundColor:['#2d6a4f','#f39c12']
}]
}
});

</script>

</body>
</html>
