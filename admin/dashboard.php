
<?php
require '../includes/auth.php';
require '../db.php';

/* TOTALS */
$totalReports = $db->reports->countDocuments();
$totalSurrenders = $db->surrenders->countDocuments();
$totalAdoptions = $db->adoptions->countDocuments();
$totalCases = $totalReports + $totalSurrenders + $totalAdoptions;

/* SAMPLE SPLIT (you can later make real DB fields) */
$domesticCases = floor($totalCases * 0.7);
$wildlifeCases = $totalCases - $domesticCases;

/* MONTH DATA */
$r_pending = $db->reports->countDocuments(['status'=>'pending']);
$r_approved = $db->reports->countDocuments(['status'=>'approved']);
$r_rejected = $db->reports->countDocuments(['status'=>'rejected']);
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

/* MAIN */
.main{
    margin-left:260px;
    padding:25px;
}

/* HEADER */
.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.search{
    padding:10px;
    border-radius:8px;
    border:1px solid #ddd;
}

.profile{
    display:flex;
    align-items:center;
    gap:10px;
}

/* STATS */
.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:20px;
}

.card{
    background:white;
    padding:20px;
    border-radius:16px;
}

.card small{
    color:#777;
}

/* CHART GRID */
.grid{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:20px;
    margin-top:20px;
}

/* BADGE */
.badge{
    padding:4px 8px;
    border-radius:8px;
    font-size:11px;
}

.domestic{background:#e6f4ea;color:#2d6a4f;}
.wildlife{background:#fdebd0;color:#e67e22;}

</style>
</head>

<body>

<div class="sidebar">
    <h2>🐾 ARSS</h2>
    <p>Animal Rescue Support System</p>

    <hr>

    <p>MAIN MENU</p>
    <a>Dashboard</a><br>
    <a>Reports</a><br>
    <a>Surrenders</a><br>
    <a>Adoptions</a><br>
    <a>Activity Logs</a><br>
    <a>Events</a>

    <hr>

    <p>ROLE & ACCESS</p>
    <div class="card">
        Domestic Admin<br>
        <small>Domestic Only</small>
    </div>

    <hr>

    <p>ADMIN TOOLS</p>
    <a>Case Reassignment</a><br>
    <a>Analytics</a>
</div>

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
    Total Reports<br>
    <h2><?= $totalReports ?></h2>
    <small>Domestic: <?= floor($totalReports*0.7) ?> | Wildlife: <?= floor($totalReports*0.3) ?></small>
</div>

<div class="card">
    Total Surrenders<br>
    <h2><?= $totalSurrenders ?></h2>
    <small>Domestic: <?= floor($totalSurrenders*0.7) ?> | Wildlife: <?= floor($totalSurrenders*0.3) ?></small>
</div>

<div class="card">
    Total Adoptions<br>
    <h2><?= $totalAdoptions ?></h2>
    <small>Domestic: <?= floor($totalAdoptions*0.7) ?> | Wildlife: <?= floor($totalAdoptions*0.3) ?></small>
</div>

<div class="card">
    Total Cases<br>
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
