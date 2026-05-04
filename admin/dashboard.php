<?php
require '../includes/auth.php';
require '../db.php';

/* DATE RANGE */
$start = new MongoDB\BSON\UTCDateTime(strtotime(date('Y-m-01 00:00:00')) * 1000);
$end   = new MongoDB\BSON\UTCDateTime(strtotime(date('Y-m-t 23:59:59')) * 1000);

/* TOTAL */
$totalReports = $db->reports->countDocuments();
$totalSurrenders = $db->surrenders->countDocuments();
$totalAdoptions = $db->adoptions->countDocuments();

/* MONTH DATA */
$r_pending = $db->reports->countDocuments(['status'=>'pending','created_at'=>['$gte'=>$start,'$lte'=>$end]]);
$r_approved = $db->reports->countDocuments(['status'=>'approved','created_at'=>['$gte'=>$start,'$lte'=>$end]]);
$r_rejected = $db->reports->countDocuments(['status'=>'rejected','created_at'=>['$gte'=>$start,'$lte'=>$end]]);

$s_pending = $db->surrenders->countDocuments(['status'=>'pending','created_at'=>['$gte'=>$start,'$lte'=>$end]]);
$s_approved = $db->surrenders->countDocuments(['status'=>'approved','created_at'=>['$gte'=>$start,'$lte'=>$end]]);
$s_rejected = $db->surrenders->countDocuments(['status'=>'rejected','created_at'=>['$gte'=>$start,'$lte'=>$end]]);

$a_pending = $db->adoptions->countDocuments(['status'=>'pending','created_at'=>['$gte'=>$start,'$lte'=>$end]]);
$a_approved = $db->adoptions->countDocuments(['status'=>'approved','created_at'=>['$gte'=>$start,'$lte'=>$end]]);
$a_rejected = $db->adoptions->countDocuments(['status'=>'rejected','created_at'=>['$gte'=>$start,'$lte'=>$end]]);
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

/* RESET */
*{box-sizing:border-box;}

body{
    margin:0;
    font-family:'Segoe UI', sans-serif;
    background:linear-gradient(to right,#f4f6f5,#eef2ef);
}

/* SIDEBAR */
.sidebar{
    width:260px;
    height:100vh;
    background:linear-gradient(180deg,#1b4332,#2d6a4f);
    color:white;
    padding:20px;
    position:fixed;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    transition:0.3s;
    z-index:1000;
}

.logo{
    font-size:clamp(18px,2vw,22px);
    font-weight:bold;
    margin-bottom:25px;
}

.menu{
    display:flex;
    flex-direction:column;
    gap:10px;
}

.menu a{
    display:flex;
    align-items:center;
    gap:12px;
    padding:12px;
    border-radius:12px;
    color:white;
    text-decoration:none;
}

.menu a:hover{background:rgba(255,255,255,0.15);}
.menu .active{background:rgba(255,255,255,0.25);}

/* MAIN */
.main{
    margin-left:260px;
    padding:clamp(15px,2vw,30px);
    min-height:100vh;
    transition:0.3s;
}

.container{
    max-width:1400px;
    margin:auto;
}

/* TEXT */
h1{
    font-size:clamp(20px,3vw,32px);
}

/* GRID */
.stats,.charts,.insight-grid{
    display:grid;
    gap:20px;
}

.stats{grid-template-columns:repeat(auto-fit,minmax(250px,1fr));}
.charts{grid-template-columns:repeat(auto-fit,minmax(300px,1fr));}
.insight-grid{grid-template-columns:repeat(auto-fit,minmax(200px,1fr));}

/* CARDS */
.stat,.chart-card,.insights{
    background:white;
    border-radius:18px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

.stat{
    padding:20px;
    display:flex;
    justify-content:space-between;
    align-items:center;
}

.chart-card,.insights{
    padding:20px;
}

/* ICON */
.icon{
    padding:12px;
    border-radius:50%;
}

.green{background:#e6f4ea;color:#2d6a4f;}
.orange{background:#fdebd0;color:#e67e22;}
.red{background:#fde2e2;color:#e74c3c;}

.tag{
    font-size:12px;
    background:#eef2ef;
    padding:6px 10px;
    border-radius:10px;
}

/* CHART */
canvas{
    width:100% !important;
    height:220px !important;
}

/* TABLET */
@media(max-width:1024px){
    .sidebar{width:220px;}
    .main{margin-left:220px;}
}

/* MOBILE */
@media(max-width:768px){
    .sidebar{left:-260px;}
    .sidebar.active{left:0;}
    .main{margin-left:0;}
    .stats,.charts,.insight-grid{grid-template-columns:1fr;}
}

/* SMALL MOBILE */
@media(max-width:480px){
    .stat{
        flex-direction:column;
        align-items:flex-start;
    }
}

</style>
</head>

<body>

<!-- MOBILE MENU BUTTON -->
<button onclick="toggleMenu()" style="
position:fixed;
top:15px;
left:15px;
z-index:1100;
background:#2d6a4f;
color:white;
border:none;
padding:10px;
border-radius:8px;">
☰
</button>

<div class="sidebar">
    <div>
        <div class="logo"><i class="fas fa-paw"></i> ARSS</div>

        <div class="menu">
            <a class="active"><i class="fas fa-chart-bar"></i> Dashboard</a>
            <a href="reports.php"><i class="fas fa-flag"></i> Reports</a>
            <a href="surrenders.php"><i class="fas fa-box"></i> Surrenders</a>
            <a href="history.php"><i class="fas fa-clock"></i> Activity Logs</a>
            <a href="events.php"><i class="fas fa-calendar"></i> Events</a>
        </div>
    </div>

    <a href="logout.php" class="logout">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</div>

<div class="main">
<div class="container">

<div class="top">
    <p>Welcome back, Admin 👋</p>
    <h1>Admin Dashboard</h1>
</div>

<div class="stats">

<div class="stat">
<div class="stat-left">
<div class="icon green">📄</div>
<div>
<p>Total Reports</p>
<h2><?= $totalReports ?></h2>
</div>
</div>
<div class="tag">All time</div>
</div>

<div class="stat">
<div class="stat-left">
<div class="icon orange">📦</div>
<div>
<p>Total Surrenders</p>
<h2><?= $totalSurrenders ?></h2>
</div>
</div>
<div class="tag">All time</div>
</div>

<div class="stat">
<div class="stat-left">
<div class="icon red">❤️</div>
<div>
<p>Total Adoptions</p>
<h2><?= $totalAdoptions ?></h2>
</div>
</div>
<div class="tag">All time</div>
</div>

</div>

<div class="charts">

<div class="chart-card">
<h3>Reports (This Month)</h3>
<canvas id="rChart"></canvas>
</div>

<div class="chart-card">
<h3>Surrenders (This Month)</h3>
<canvas id="sChart"></canvas>
</div>

<div class="chart-card">
<h3>Adoptions (This Month)</h3>
<canvas id="aChart"></canvas>
</div>

</div>

<div class="insights">
<h3>Quick Insights</h3>

<div class="insight-grid">
<div class="insight">Pending Reports: <?= $r_pending ?></div>
<div class="insight">Pending Surrenders: <?= $s_pending ?></div>
<div class="insight">Pending Adoptions: <?= $a_pending ?></div>
</div>

</div>

</div>
</div>

<script>
function toggleMenu(){
    document.querySelector('.sidebar').classList.toggle('active');
}

const options={scales:{y:{beginAtZero:true}}};

new Chart(rChart,{type:'bar',data:{labels:['Pending','Approved','Rejected'],
datasets:[{data:[<?= $r_pending ?>,<?= $r_approved ?>,<?= $r_rejected ?>]}]},options});

new Chart(sChart,{type:'bar',data:{labels:['Pending','Approved','Rejected'],
datasets:[{data:[<?= $s_pending ?>,<?= $s_approved ?>,<?= $s_rejected ?>]}]},options});

new Chart(aChart,{type:'bar',data:{labels:['Pending','Approved','Rejected'],
datasets:[{data:[<?= $a_pending ?>,<?= $a_approved ?>,<?= $a_rejected ?>]}]},options});
</script>

</body>
</html>
```
