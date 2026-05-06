<?php
require '../includes/auth.php';
require '../db.php';

$collection = $db->events;

/* ADD EVENT */
if ($_SERVER["REQUEST_METHOD"]=="POST"){

    $collection->insertOne([
        'title' => $_POST['title'],
        'location' => $_POST['location'],
        'date' => $_POST['date'],
        'description' => $_POST['description'],
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ]);

    header("Location: events.php");
    exit;
}

/* DELETE */
if(isset($_GET['delete'])){

    $collection->deleteOne([
        '_id'=>new MongoDB\BSON\ObjectId($_GET['delete'])
    ]);

    header("Location: events.php");
    exit;
}

/* DATA */
$events=$collection->find([],['sort'=>['_id'=>-1]]);
$total=$collection->countDocuments();
?>

<!DOCTYPE html>
<html>
<head>
<title>Events Dashboard</title>

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
margin-bottom:15px;
}

/* GRID */
.grid{
display:grid;
grid-template-columns:2fr 1fr;
gap:20px;
}

/* FORM */
input,textarea{
width:100%;
padding:12px;
margin-bottom:12px;
border-radius:10px;
border:1px solid #ddd;
font-family:'Segoe UI';
}

button{
padding:12px 18px;
background:#2d6a4f;
color:white;
border:none;
border-radius:10px;
cursor:pointer;
}

button:hover{
opacity:.9;
}

.delete-btn{
display:inline-block;
margin-top:10px;
padding:8px 12px;
background:#d00000;
color:white;
border-radius:8px;
text-decoration:none;
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

<a href="history.php">
<i class="fas fa-clock"></i>
Activity Logs
</a>

<a href="events.php" class="active">
<i class="fas fa-calendar"></i>
Events
</a>

<div class="section-title">ROLE & ACCESS</div>

<div class="role-card">

<strong>
<?= strtoupper(str_replace('_',' ', $_SESSION['role'] ?? 'admin')) ?>
</strong>

<br>

<small>Event Management Access</small>

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

<input type="text" class="search" placeholder="Search events...">

<div class="profile">

<img src="../assets/images/admin.png">

<div>
<strong>Admin</strong>
<br>
<small><?= strtoupper($_SESSION['role'] ?? 'admin') ?></small>
</div>

</div>

</div>

<h1>Events Dashboard</h1>

<br>

<!-- STATS -->
<div class="stats">

<div class="card">
<h3>Total Events</h3>
<h1><?= $total ?></h1>
</div>

</div>

<!-- GRID -->
<div class="grid">

<!-- LEFT -->
<div>

<div class="card" style="margin-bottom:20px;">

<h3>Add Event</h3>

<form method="POST">

<input type="text" name="title" placeholder="Event Title" required>

<input type="text" name="location" placeholder="Location" required>

<input type="date" name="date" required>

<textarea name="description" placeholder="Event Description" required></textarea>

<button type="submit">
Add Event
</button>

</form>

</div>

<?php foreach($events as $e): ?>

<div class="card" style="margin-bottom:15px;">

<h3><?= $e['title'] ?></h3>

<p>
<i class="fas fa-location-dot"></i>
<?= $e['location'] ?>
</p>

<br>

<p>
<i class="fas fa-calendar"></i>
<?= $e['date'] ?>
</p>

<br>

<p><?= $e['description'] ?></p>

<a href="?delete=<?= $e['_id'] ?>" class="delete-btn">
Delete Event
</a>

</div>

<?php endforeach; ?>

</div>

<!-- RIGHT -->
<div>

<div class="card">

<h3>Event Overview</h3>

<p>📅 Total Events: <?= $total ?></p>

<br>

<p>
Manage community rescue activities, awareness campaigns, and adoption drives.
</p>

</div>

<br>

<div class="card">

<h3>System Notes</h3>

<p>
Only authorized admins can create and manage events.
</p>

</div>

</div>

</div>

</div>

</body>
</html>
