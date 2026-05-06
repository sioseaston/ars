<?php
require '../includes/auth.php';
require '../db.php';

$totalReports = $db->reports->countDocuments();
$totalSurrenders = $db->surrenders->countDocuments();
$totalEvents = $db->events->countDocuments();

$pendingReports = $db->reports->countDocuments(['status'=>'pending']);
$approvedReports = $db->reports->countDocuments(['status'=>'approved']);
$rejectedReports = $db->reports->countDocuments(['status'=>'rejected']);
?>

<!DOCTYPE html>
<html>
<head>
<title>Analytics Dashboard</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>

*{
box-sizing:border-box;
margin:0;
padding:0;
}

body{
font-family:'Segoe UI';
background:#f4f6f5;
display:flex;
}

/* SIDEBAR */
.sidebar{
width:260px;
height:100vh;
background:linear-gradient(180deg,#1b4332,#2d6a4f);
color:white;
padding:20px;
position:fixed;
left:0;
top:0;
overflow:auto;
}

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

.section-title{
font-size:12px;
opacity:.7;
margin:25px 0 10px;
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

.role-card{
background:rgba(255,255,255,.12);
padding:15px;
border-radius:16px;
margin-top:10px;
}

.role-card small{
opacity:.8;
}

/* MAIN */
.main{
margin-left:260px;
padding:25px;
width:100%;
}

/* TOPBAR */
.topbar{
display:flex;
justify-content:space-between;
align-items:center;
margin-bottom:30px;
flex-wrap:wrap;
gap:15px;
}

.search{
padding:12px;
border:none;
border-radius:12px;
width:280px;
}

.profile{
display:flex;
align-items:center;
gap:12px;
}

.profile img{
width:50px;
height:50px;
border-radius:50%;
object-fit:cover;
}

/* STATS */
.stats{
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:20px;
margin-bottom:20px;
}

.card{
background:white;
padding:22px;
border-radius:18px;
box-shadow:0 5px 15px rgba(0,0,0,.05);
}

.card h3{
margin-bottom:10px;
}

/* GRID */
.grid{
display:grid;
grid-template-columns:2fr 1fr;
gap:20px;
}

/* RESPONSIVE */
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

}

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">

<div class="logo">
🐾 ARSS
<small>Animal Rescue Support System</small>
</div>

<div class="section-title">MAIN MENU</div>

<a href="dashboard.php">
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

<a href="history.php">
<i class="fas fa-clock"></i>
Activity Logs
</a>

<a href="events.php">
<i class="fas fa-calendar"></i>
Events
</a>

<div class="section-title">ROLE & ACCESS</div>

<div class="role-card">
<strong>
<?= strtoupper(str_replace('_',' ', $_SESSION['role'] ?? 'admin')) ?>
</strong>
<br>
<small>Analytics Access</small>
</div>

<?php if(($_SESSION['role'] ?? '') == 'super_admin'): ?>

<div class="section-title">ADMIN TOOLS</div>

<a href="reassign.php">
<i class="fas fa-random"></i>
Case Reassignment
</a>

<a href="analytics.php" class="active">
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

<input type="text" class="search" placeholder="Search analytics...">

<div class="profile">
<img src="../assets/images/admin.png">
<div>
<strong>Admin</strong>
<br>
<small><?= strtoupper($_SESSION['role'] ?? 'admin') ?></small>
</div>
</div>

</div>

<h1>Analytics Dashboard</h1>

<br>

<!-- STATS -->
<div class="stats">

<div class="card">
<h3>Total Reports</h3>
<h1><?= $totalReports ?></h1>
</div>

<div class="card">
<h3>Total Surrenders</h3>
<h1><?= $totalSurrenders ?></h1>
</div>

<div class="card">
<h3>Total Events</h3>
<h1><?= $totalEvents ?></h1>
</div>

</div>

<!-- GRID -->
<div class="grid">

<div class="card">
<h3>Reports Analytics</h3>
<canvas id="reportChart"></canvas>
</div>

<div class="card">

<h3>System Insights</h3>

<p>📌 Pending Reports: <?= $pendingReports ?></p>
<br>

<p>✅ Approved Reports: <?= $approvedReports ?></p>
<br>

<p>❌ Rejected Reports: <?= $rejectedReports ?></p>

</div>

</div>

</div>

<script>

new Chart(document.getElementById('reportChart'),{
type:'bar',

data:{
labels:['Pending','Approved','Rejected'],

datasets:[{
label:'Reports',
data:[
<?= $pendingReports ?>,
<?= $approvedReports ?>,
<?= $rejectedReports ?>
]
}]
},

options:{
responsive:true
}

});

</script>

</body>
</html>
