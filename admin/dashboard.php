<?php
require '../includes/auth.php';
require '../db.php';

/* ===== ROLE FILTER FUNCTION ===== */
function getRoleFilter() {

    if (!isset($_SESSION['role'])) {
        return [];
    }

    if ($_SESSION['role'] == 'domestic_admin') {
        return ['animal_category' => 'domestic'];
    }

    return [];
}

/* ===== APPLY ROLE FILTER ===== */
$filter = getRoleFilter();

/* ===== TOTALS ===== */
$totalReports = $db->reports->countDocuments($filter);
$totalSurrenders = $db->surrenders->countDocuments($filter);

/* TOTAL CASES */
$totalCases = $totalReports + $totalSurrenders;

/* SPLIT */
$domesticReports = floor($totalReports * 0.7);
$wildlifeReports = $totalReports - $domesticReports;

$domesticSurrenders = floor($totalSurrenders * 0.7);
$wildlifeSurrenders = $totalSurrenders - $domesticSurrenders;

$domesticCases = $domesticReports + $domesticSurrenders;
$wildlifeCases = $totalCases - $domesticCases;

/* STATUS */
$r_pending = $db->reports->countDocuments(
    array_merge($filter, ['status'=>'pending'])
);

$r_approved = $db->reports->countDocuments(
    array_merge($filter, ['status'=>'approved'])
);

$r_rejected = $db->reports->countDocuments(
    array_merge($filter, ['status'=>'rejected'])
);

$s_pending = $db->surrenders->countDocuments(
    array_merge($filter, ['status'=>'pending'])
);

/* ACTIVITY LOGS */
$logs = $db->logs->find([], [
    'sort'=>['time'=>-1],
    'limit'=>3
]);
?>

<!DOCTYPE html>
<html>
<head>

<title>Admin Dashboard</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

/* ===== GLOBAL ===== */
*{
box-sizing:border-box;
margin:0;
padding:0;
}

body{
font-family:'Segoe UI',sans-serif;
background:#f4f6f5;
display:flex;
}

/* ===== SIDEBAR ===== */
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

/* LOGO */
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
opacity:.8;
}

/* SECTION */
.section-title{
font-size:12px;
opacity:.7;
margin:25px 0 10px;
}

/* MENU */
.sidebar a{
display:flex;
align-items:center;
gap:12px;
padding:14px;
margin-bottom:8px;
border-radius:14px;
color:white;
text-decoration:none;
transition:.2s;
}

.sidebar a:hover{
background:rgba(255,255,255,.1);
transform:translateX(3px);
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

/* ===== MAIN ===== */
.main{
margin-left:260px;
padding:25px;
width:100%;
}

/* ===== TOPBAR ===== */
.topbar{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:25px;
flex-wrap:wrap;
gap:15px;
}

.search{
padding:12px;
border-radius:12px;
border:1px solid #ddd;
width:280px;
}

.profile{
display:flex;
align-items:center;
gap:12px;
}

.profile img{
width:45px;
height:45px;
border-radius:50%;
object-fit:cover;
}

/* ===== STATS ===== */
.stats{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(230px,1fr));
gap:20px;
}

.card{
background:white;
padding:20px;
border-radius:18px;
box-shadow:0 5px 15px rgba(0,0,0,.05);
}

.icon{
width:45px;
height:45px;
border-radius:50%;
display:flex;
align-items:center;
justify-content:center;
margin-bottom:10px;
}

.green{
background:#e6f4ea;
color:#2d6a4f;
}

.orange{
background:#fdebd0;
color:#e67e22;
}

.blue{
background:#e3f2fd;
color:#3498db;
}

/* ===== GRID ===== */
.grid{
display:grid;
grid-template-columns:2fr 1fr;
gap:20px;
margin-top:20px;
}

.grid2{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
gap:20px;
margin-top:20px;
}

/* ===== BADGES ===== */
.badge{
padding:6px 12px;
border-radius:12px;
font-size:12px;
display:inline-block;
}

.domestic{
background:#d8f3dc;
color:#1b4332;
}

.wildlife{
background:#fdebd0;
color:#e67e22;
}

/* ===== ALERT ===== */
.alert{
background:#fff3cd;
padding:15px;
border-radius:12px;
margin-top:15px;
}

/* ===== BUTTON ===== */
button{
background:#2d6a4f;
color:white;
border:none;
padding:10px 14px;
border-radius:10px;
cursor:pointer;
margin-top:10px;
}

button:hover{
opacity:.9;
}

/* ===== RESPONSIVE ===== */
@media(max-width:900px){

.sidebar{
width:100%;
height:auto;
position:relative;
}

.main{
margin-left:0;
}

.grid{
grid-template-columns:1fr;
}

.topbar{
flex-direction:column;
align-items:flex-start;
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

<!-- LOGO -->
<div class="logo">
🐾 ARSS
<small>Animal Rescue Support System</small>
</div>

<!-- MENU -->
<div class="section-title">MAIN MENU</div>

<a href="dashboard.php" class="active">
<i class="fas fa-home"></i>
Dashboard
</a>

<a href="reports.php">
<i class="fas fa-flag"></i>
Reports
</a>

<a href="surrenders.php">
<i class="fas fa-box"></i>
Surrenders
</a>

<a href="activitylog.php">
<i class="fas fa-clock"></i>
Activity Logs
</a>

<a href="events.php">
<i class="fas fa-calendar"></i>
Events
</a>

<!-- ROLE -->
<div class="section-title">ROLE & ACCESS</div>

<div class="role-card">

<strong>
<?= strtoupper(str_replace('_',' ', $_SESSION['role'] ?? 'admin')) ?>
</strong>

<br>

<small>

<?php

if(($_SESSION['role'] ?? '') == 'domestic_admin'){
    echo "Domestic Only";
}
elseif(($_SESSION['role'] ?? '') == 'admin'){
    echo "General Access";
}
elseif(($_SESSION['role'] ?? '') == 'super_admin'){
    echo "Full System Access";
}

?>

</small>

</div>

<!-- ADMIN TOOLS -->
<?php if(($_SESSION['role'] ?? '') == 'super_admin'): ?>

<div class="section-title">ADMIN TOOLS</div>

<a href="reassign.php">
<i class="fas fa-random"></i>
Case Reassignment
</a>

<a href="analytics.php">
<i class="fas fa-chart-line"></i>
Analytics
</a>

<?php endif; ?>

<br>

<a href="logout.php">
<i class="fas fa-sign-out-alt"></i>
Logout
</a>

</div>

<!-- MAIN -->
<div class="main">

<!-- TOPBAR -->
<div class="topbar">

<input class="search" placeholder="Search here...">

<div class="profile">

<img src="https://i.pravatar.cc/45">

<div>
<strong>Admin</strong>
<br>
<small><?= strtoupper($_SESSION['role'] ?? 'admin') ?></small>
</div>

</div>

</div>

<h1>Admin Dashboard</h1>

<br>

<!-- STATS -->
<div class="stats">

<div class="card">

<div class="icon green">
<i class="fas fa-file"></i>
</div>

<h3>Total Reports</h3>

<h1><?= $totalReports ?></h1>

<small>
Domestic: <?= $domesticReports ?> |
Wildlife: <?= $wildlifeReports ?>
</small>

</div>

<div class="card">

<div class="icon orange">
<i class="fas fa-box"></i>
</div>

<h3>Total Surrenders</h3>

<h1><?= $totalSurrenders ?></h1>

<small>
Domestic: <?= $domesticSurrenders ?> |
Wildlife: <?= $wildlifeSurrenders ?>
</small>

</div>

<div class="card">

<div class="icon blue">
<i class="fas fa-chart-line"></i>
</div>

<h3>Total Cases</h3>

<h1><?= $totalCases ?></h1>

<small>
Domestic: <?= $domesticCases ?> |
Wildlife: <?= $wildlifeCases ?>
</small>

</div>

</div>

<!-- CHARTS -->
<div class="grid">

<div class="card">

<h3>Reports Analytics</h3>

<canvas id="rChart"></canvas>

</div>

<div class="card">

<h3>Case Distribution</h3>

<canvas id="pieChart"></canvas>

</div>

</div>

<!-- LOWER -->
<div class="grid2">

<!-- QUICK INSIGHTS -->
<div class="card">

<h3>Quick Insights</h3>

<p>📌 Pending Reports: <?= $r_pending ?></p>
<br>

<p>📦 Pending Surrenders: <?= $s_pending ?></p>
<br>

<p>📊 Total Active Cases: <?= $totalCases ?></p>

</div>

<!-- ACTIVITY -->
<div class="card">

<h3>Recent Activity</h3>

<?php foreach($logs as $log): ?>

<p>
✅ <?= $log['action'] ?? 'System Activity' ?>
</p>

<small>
<?= strtoupper($log['admin'] ?? 'ADMIN') ?>
</small>

<hr style="margin:10px 0;">

<?php endforeach; ?>

</div>

<!-- CASE TAGGING -->
<div class="card">

<h3>Case Tagging Overview</h3>

<span class="badge domestic">
Domestic <?= $domesticCases ?>
</span>

<br><br>

<span class="badge wildlife">
Wildlife <?= $wildlifeCases ?>
</span>

<div class="alert">

<p>
<strong>Escalation System</strong>
</p>

<br>

<p>
Reassign misclassified cases
</p>

<?php if(($_SESSION['role'] ?? '') == 'super_admin'): ?>

<a href="reassign.php">
<button>
Go to Reassignment
</button>
</a>

<?php endif; ?>

</div>

</div>

</div>

</div>

<script>

new Chart(document.getElementById('rChart'),{

type:'bar',

data:{
labels:['Pending','Approved','Rejected'],

datasets:[{
label:'Reports',
data:[
<?= $r_pending ?>,
<?= $r_approved ?>,
<?= $r_rejected ?>
],
backgroundColor:[
'#2d6a4f',
'#4CAF50',
'#d00000'
]
}]
}

});

new Chart(document.getElementById('pieChart'),{

type:'doughnut',

data:{
labels:['Domestic','Wildlife'],

datasets:[{
data:[
<?= $domesticCases ?>,
<?= $wildlifeCases ?>
],
backgroundColor:[
'#2d6a4f',
'#f39c12'
]
}]
}

});

</script>

</body>
</html>
