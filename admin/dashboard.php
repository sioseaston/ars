```php
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

/* ===== BODY ===== */
body{
    margin:0;
    font-family:'Segoe UI';
    background:linear-gradient(to right, #f4f6f5, #eef2ef);
}

/* ===== SIDEBAR ===== */
.sidebar{
    width:260px;
    height:100vh;
    background:linear-gradient(180deg,#1b4332,#2d6a4f);
    color:white;
    padding:20px;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    position:fixed;
    top:0;
    left:0;
    overflow-y:auto;
}

.logo{
    font-size:22px;
    font-weight:bold;
    margin-bottom:25px;
}

.logo i{
    margin-right:8px;
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
    padding:12px 14px;
    border-radius:12px;
    color:white;
    text-decoration:none;
    transition:0.2s;
}

.menu a:hover{
    background:rgba(255,255,255,0.15);
    transform:translateX(4px);
}

.menu .active{
    background:rgba(255,255,255,0.25);
}

.menu i{
    width:20px;
    text-align:center;
}

.logout{
    padding:12px;
    border-radius:12px;
    background:rgba(255,255,255,0.1);
    text-align:center;
    text-decoration:none;
    color:white;
    margin-top:20px;
}

.logout:hover{
    background:rgba(255,255,255,0.25);
}

/* ===== MAIN ===== */
.main{
    margin-left:260px;
    padding:30px;
    min-height:100vh;
}

.container{
    max-width:1400px;
    margin:0 auto;
    padding:10px;
}

/* HEADER */
.top{
    margin-bottom:20px;
}

h1{
    margin:5px 0 15px;
}

/* ===== STATS ===== */
.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(250px, 1fr));
    gap:20px;
}

.stat{
    background:white;
    padding:22px;
    border-radius:18px;
    display:flex;
    justify-content:space-between;
    align-items:center;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
    height:100%;
}

.stat-left{
    display:flex;
    gap:15px;
    align-items:center;
}

.stat h2{
    margin:5px 0 0;
}

.icon{
    padding:15px;
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

/* ===== CHARTS ===== */
.charts{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(300px, 1fr));
    gap:20px;
    margin-top:25px;
}

.chart-card{
    background:white;
    padding:20px;
    border-radius:18px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
    height:100%;
}

canvas{
    width:100% !important;
    height:220px !important;
}

.summary{
    margin-top:10px;
    font-size:13px;
    display:flex;
    justify-content:space-between;
}

/* ===== INSIGHTS ===== */
.insights{
    margin-top:25px;
    background:white;
    padding:20px;
    border-radius:18px;
    box-shadow:0 10px 25px rgba(0,0,0,0.05);
}

.insight-grid{
    display:grid;
    grid-template-columns:repeat(auto-fit, minmax(200px, 1fr));
    gap:15px;
    margin-top:10px;
}

.insight{
    background:#f9faf9;
    padding:15px;
    border-radius:12px;
}

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
        <div class="logo"><i class="fas fa-paw"></i> ARSS</div>

        <div class="menu">
            <a class="active"><i class="fas fa-chart-bar"></i> Dashboard</a>
            <a href="reports.php"><i class="fas fa-flag"></i> Manage Reports</a>
            <a href="surrenders.php"><i class="fas fa-box"></i> Manage Surrenders</a>
            <a href="adoptions.php"><i class="fas fa-heart"></i> Manage Adoptions</a>
            <a href="events.php"><i class="fas fa-calendar"></i> Manage Events</a>
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
<div class="summary">
<span><?= $r_pending ?> Pending</span>
<span><?= $r_approved ?> Approved</span>
<span><?= $r_rejected ?> Rejected</span>
</div>
</div>

<div class="chart-card">
<h3>Surrenders (This Month)</h3>
<canvas id="sChart"></canvas>
<div class="summary">
<span><?= $s_pending ?> Pending</span>
<span><?= $s_approved ?> Approved</span>
<span><?= $s_rejected ?> Rejected</span>
</div>
</div>

<div class="chart-card">
<h3>Adoptions (This Month)</h3>
<canvas id="aChart"></canvas>
<div class="summary">
<span><?= $a_pending ?> Pending</span>
<span><?= $a_approved ?> Approved</span>
<span><?= $a_rejected ?> Rejected</span>
</div>
</div>

</div>

<div class="insights">
<h3>Quick Insights</h3>

<div class="insight-grid">

<div class="insight">
<strong>Most Reports</strong><br>
Pending (<?= $r_pending ?>)
</div>

<div class="insight">
<strong>Surrender Rate</strong><br>
<?= $totalReports ? round(($totalSurrenders/$totalReports)*100) : 0 ?>%
</div>

<div class="insight">
<strong>Adoption Rate</strong><br>
<?= $totalReports ? round(($totalAdoptions/$totalReports)*100) : 0 ?>%
</div>

<div class="insight">
<strong>Active This Month</strong><br>
<?= $r_pending + $s_pending + $a_pending ?>
</div>

</div>
</div>

</div>
</div>

<script>
const options={
    scales:{y:{beginAtZero:true,ticks:{stepSize:1,precision:0}}}
};

new Chart(rChart,{
type:'bar',
data:{
    labels:['Pending','Approved','Rejected'],
    datasets:[{
        label:'Reports',
        data:[<?= $r_pending ?>,<?= $r_approved ?>,<?= $r_rejected ?>],
        backgroundColor:['#f39c12','#2ecc71','#e74c3c']
    }]
},
options:options
});

new Chart(sChart,{
type:'bar',
data:{
    labels:['Pending','Approved','Rejected'],
    datasets:[{
        label:'Surrenders',
        data:[<?= $s_pending ?>,<?= $s_approved ?>,<?= $s_rejected ?>],
        backgroundColor:['#f39c12','#2ecc71','#e74c3c']
    }]
},
options:options
});

new Chart(aChart,{
type:'bar',
data:{
    labels:['Pending','Approved','Rejected'],
    datasets:[{
        label:'Adoptions',
        data:[<?= $a_pending ?>,<?= $a_approved ?>,<?= $a_rejected ?>],
        backgroundColor:['#f39c12','#2ecc71','#e74c3c']
    }]
},
options:options
});
</script>

</body>
</html>
```
