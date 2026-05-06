<?php
require '../includes/auth.php';
require '../db.php';

/* GET LOGS */
$logs = $db->logs->find([], [
    'sort' => ['time' => -1]
]);
?>

<!DOCTYPE html>
<html>
<head>
<title>Activity Logs</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
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

/* CARDS */
.card{
background:white;
padding:22px;
border-radius:18px;
margin-bottom:20px;
box-shadow:0 5px 15px rgba(0,0,0,.05);
}

.card h3{
margin-bottom:10px;
color:#1b4332;
}

/* BADGES */
.badge{
padding:6px 12px;
border-radius:12px;
font-size:12px;
display:inline-block;
margin-top:10px;
background:#d8f3dc;
color:#1b4332;
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

<a href="activitylog.php" class="active">
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

<small>Activity Monitoring Access</small>

</div>

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

<input type="text" class="search" placeholder="Search activity logs...">

<div class="profile">

<img src="../assets/images/admin.png">

<div>
<strong>Admin</strong>
<br>
<small><?= strtoupper($_SESSION['role'] ?? 'admin') ?></small>
</div>

</div>

</div>

<h1>Activity Logs</h1>

<br>

<?php
$count = 0;

foreach($logs as $log):

$count++;
?>

<div class="card">

<h3>
<?= $log['action'] ?? 'System Activity' ?>
</h3>

<p>
Animal: <?= $log['animal'] ?? 'N/A' ?>
</p>

<br>

<p>
Admin Role:
<span class="badge">
<?= strtoupper($log['admin'] ?? 'ADMIN') ?>
</span>
</p>

<br>

<p>
Time:
<?= isset($log['time']) ? $log['time']->toDateTime()->format('M d, Y h:i A') : 'N/A' ?>
</p>

</div>

<?php endforeach; ?>

<?php if($count == 0): ?>

<div class="card">
No activity logs found.
</div>

<?php endif; ?>

</div>

</body>
</html>
