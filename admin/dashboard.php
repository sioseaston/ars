<?php
require '../includes/auth.php';
require '../db.php';

/* ===== TOTALS ===== */
$totalReports = $db->reports->countDocuments();
$totalSurrenders = $db->surrenders->countDocuments();

/* REMOVE ADOPTIONS */
$totalCases = $totalReports + $totalSurrenders;

/* SAMPLE SPLIT */
$domesticReports = floor($totalReports * 0.7);
$wildlifeReports = $totalReports - $domesticReports;

$domesticSurrenders = floor($totalSurrenders * 0.7);
$wildlifeSurrenders = $totalSurrenders - $domesticSurrenders;

$domesticCases = $domesticReports + $domesticSurrenders;
$wildlifeCases = $totalCases - $domesticCases;

/* STATUS */
$r_pending = $db->reports->countDocuments(['status'=>'pending']);
$r_approved = $db->reports->countDocuments(['status'=>'approved']);
$r_rejected = $db->reports->countDocuments(['status'=>'rejected']);

$s_pending = $db->surrenders->countDocuments(['status'=>'pending']);
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Dashboard</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

/* ===== RESET ===== */
*{
    box-sizing:border-box;
    margin:0;
    padding:0;
}

/* ===== BODY ===== */
body{
    font-family:'Segoe UI', sans-serif;
    background:#f4f6f5;
    color:#333;
}

/* ===== SIDEBAR ===== */
.sidebar{
    width:260px;
    height:100vh;
    position:fixed;
    top:0;
    left:0;
    background:linear-gradient(180deg,#1b4332,#2d6a4f);
    color:white;
    padding:20px;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
}

.sidebar h2{
    margin-bottom:10px;
}

.sidebar a{
    display:flex;
    align-items:center;
    gap:10px;
    padding:10px 12px;
    margin:6px 0;
    border-radius:10px;
    color:white;
    text-decoration:none;
    transition:0.2s;
}

.sidebar a:hover{
    background:rgba(255,255,255,0.15);
}

.sidebar a.active{
    background:rgba(255,255,255,0.25);
}

/* ===== MAIN ===== */
.main{
    margin-left:260px;
    padding:25px;
}

/* ===== TOPBAR ===== */
.topbar{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:25px;
}

.search{
    padding:10px;
    border-radius:8px;
    border:1px solid #ddd;
    width:250px;
}

.profile{
    display:flex;
    align-items:center;
    gap:10px;
}

.profile img{
    width:35px;
    height:35px;
    border-radius:50%;
}

/* ===== STATS ===== */
.stats{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
    gap:20px;
}

.card{
    background:white;
    padding:18px;
    border-radius:16px;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
}

/* ICON + TEXT ALIGN */
.card .icon{
    width:40px;
    height:40px;
    border-radius:50%;
    display:flex;
    align-items:center;
    justify-content:center;
    margin-bottom:10px;
}

/* COLORS */
.green{background:#e6f4ea;color:#2d6a4f;}
.orange{background:#fdebd0;color:#e67e22;}
.red{background:#fde2e2;color:#e74c3c;}
.blue{background:#e3f2fd;color:#3498db;}

/* ===== GRID ===== */
.grid{
    display:grid;
    grid-template-columns:2fr 1fr;
    gap:20px;
    margin-top:25px;
}

.grid2{
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:20px;
    margin-top:25px;
}

/* ===== BADGES ===== */
.badge{
    padding:5px 10px;
    border-radius:12px;
    font-size:12px;
    display:inline-block;
    margin-top:5px;
}

.domestic{
    background:#e6f4ea;
    color:#2d6a4f;
}

.wildlife{
    background:#fdebd0;
    color:#e67e22;
}

/* ===== ALERT BOX ===== */
.alert{
    background:#fff3cd;
    padding:15px;
    border-radius:12px;
    margin-top:10px;
}

/* ===== BUTTON ===== */
button{
    background:#2d6a4f;
    color:white;
    border:none;
    padding:10px;
    border-radius:8px;
    cursor:pointer;
}

button:hover{
    opacity:0.9;
}

/* ===== TEXT ===== */
h1{
    margin-bottom:10px;
}

h3{
    margin-bottom:5px;
}

/* ===== RESPONSIVE ===== */
@media(max-width:1024px){
    .sidebar{
        width:220px;
    }

    .main{
        margin-left:220px;
    }
}

@media(max-width:768px){

    .sidebar{
        display:none;
    }

    .main{
        margin-left:0;
    }

    .grid{
        grid-template-columns:1fr;
    }

    .stats{
        grid-template-columns:1fr;
    }
}

@media(max-width:480px){

    .topbar{
        flex-direction:column;
        align-items:flex-start;
        gap:10px;
    }

    .search{
        width:100%;
    }
}

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
<h2>🐾 ARSS</h2>

<a class="active">Dashboard</a>
<a href="reports.php">Reports</a>
<a href="surrenders.php">Surrenders</a>
<a href="history.php">Activity Logs</a>
<a href="events.php">Events</a>

<br>
<a href="logout.php">Logout</a>
</div>

<!-- MAIN -->
<div class="main">

<h1>Admin Dashboard</h1>

<!-- STATS -->
<div class="stats">

<div class="card">
<div class="icon green"><i class="fas fa-file"></i></div>
<h3>Total Reports</h3>
<h2><?= $totalReports ?></h2>
</div>

<div class="card">
<div class="icon orange"><i class="fas fa-box"></i></div>
<h3>Total Surrenders</h3>
<h2><?= $totalSurrenders ?></h2>
</div>

<div class="card">
<div class="icon blue"><i class="fas fa-chart-line"></i></div>
<h3>Total Cases</h3>
<h2><?= $totalCases ?></h2>
</div>

</div>

<!-- CHART -->
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
<p>Total Cases: <?= $totalCases ?></p>
</div>

<div class="card">
<h3>Recent Activity</h3>
<p>New report submitted</p>
<p>New surrender request</p>
</div>

<div class="card">
<h3>Case Tagging</h3>
<span class="badge domestic">Domestic <?= $domesticCases ?></span><br><br>
<span class="badge wildlife">Wildlife <?= $wildlifeCases ?></span>
</div>

</div>

</div>

<script>
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
