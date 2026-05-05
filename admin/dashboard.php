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
    background:#f4f6f5;
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
}

/* MENU */
.menu{
    display:flex;
    flex-direction:column;
    gap:10px;
}
.menu a{
    display:flex;
    align-items:center;
    gap:10px;
    padding:12px;
    border-radius:10px;
    color:white;
    text-decoration:none;
}
.menu a.active{
    background:rgba(255,255,255,0.2);
}

/* TITLES */
.menu-title{
    font-size:11px;
    opacity:0.7;
    margin-top:15px;
}

/* ROLE CARD */
.role-card{
    background:rgba(255,255,255,0.1);
    padding:12px;
    border-radius:12px;
    margin-top:10px;
}

/* MAIN */
.main{
    margin-left:260px;
    padding:25px;
}

/* GRID */
.stats, .charts{
    display:grid;
    gap:20px;
}
.stats{grid-template-columns:repeat(auto-fit,minmax(250px,1fr));}
.charts{grid-template-columns:repeat(auto-fit,minmax(300px,1fr));}

/* CARDS */
.card{
    background:white;
    padding:20px;
    border-radius:16px;
}

/* BADGES */
.badge{
    display:inline-block;
    padding:4px 8px;
    border-radius:8px;
    font-size:11px;
}
.domestic{background:#e6f4ea;color:#2d6a4f;}
.wildlife{background:#fdebd0;color:#e67e22;}

/* MOBILE */
@media(max-width:768px){
    .sidebar{display:none;}
    .main{margin-left:0;}
}

</style>
</head>

<body>

<div class="sidebar">

    <div>
        <h2>🐾 ARSS</h2>

        <div class="menu">

            <div class="menu-title">MAIN MENU</div>
            <a class="active">Dashboard</a>
            <a href="reports.php">Reports</a>
            <a href="surrenders.php">Surrenders</a>
            <a href="history.php">Activity Logs</a>
            <a href="events.php">Events</a>

            <div class="menu-title">ROLE & ACCESS</div>

            <div class="role-card">
                <strong>Domestic Admin</strong><br>
                <small>Access: Domestic Animals Only</small>
            </div>

            <div class="menu-title">ADMIN TOOLS</div>

            <a href="#">Case Reassignment</a>
            <a href="#">Analytics</a>

        </div>
    </div>

    <a href="logout.php">Logout</a>

</div>

<div class="main">

<h1>Admin Dashboard</h1>

<!-- STATS -->
<div class="stats">

<div class="card">
    <p>Total Reports</p>
    <h2><?= $totalReports ?></h2>
    <span class="badge domestic">Domestic</span>
</div>

<div class="card">
    <p>Total Surrenders</p>
    <h2><?= $totalSurrenders ?></h2>
    <span class="badge wildlife">Wildlife</span>
</div>

<div class="card">
    <p>Total Adoptions</p>
    <h2><?= $totalAdoptions ?></h2>
</div>

</div>

<!-- CHARTS -->
<div class="charts">

<div class="card">
<h3>Reports</h3>
<canvas id="rChart"></canvas>
</div>

<div class="card">
<h3>Surrenders</h3>
<canvas id="sChart"></canvas>
</div>

<div class="card">
<h3>Adoptions</h3>
<canvas id="aChart"></canvas>
</div>

</div>

</div>

<script>

const options={scales:{y:{beginAtZero:true}}};

new Chart(rChart,{
type:'bar',
data:{
labels:['Pending','Approved','Rejected'],
datasets:[{data:[<?= $r_pending ?>,<?= $r_approved ?>,<?= $r_rejected ?>]}]
}});

new Chart(sChart,{
type:'bar',
data:{
labels:['Pending','Approved','Rejected'],
datasets:[{data:[<?= $s_pending ?>,<?= $s_approved ?>,<?= $s_rejected ?>]}]
}});

new Chart(aChart,{
type:'bar',
data:{
labels:['Pending','Approved','Rejected'],
datasets:[{data:[<?= $a_pending ?>,<?= $a_approved ?>,<?= $a_rejected ?>]}]
}});

</script>

</body>
</html>
```
