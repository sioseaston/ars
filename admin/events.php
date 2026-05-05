<?php
require '../includes/auth.php';
require '../db.php';

$collection = $db->events;

if ($_SERVER["REQUEST_METHOD"]=="POST"){
    $collection->insertOne($_POST);
}

if(isset($_GET['delete'])){
    $collection->deleteOne(['_id'=>new MongoDB\BSON\ObjectId($_GET['delete'])]);
}

$events=$collection->find([],['sort'=>['_id'=>-1]]);
$total=$collection->countDocuments();
?>

<!DOCTYPE html>
<html>
<head>
<title>Events</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
body{margin:0;font-family:'Segoe UI';background:#f4f6f5;}
.sidebar{width:260px;height:100vh;position:fixed;background:linear-gradient(#1b4332,#2d6a4f);color:white;padding:20px;}
.sidebar a{display:block;color:white;padding:10px;margin:5px 0;border-radius:10px;text-decoration:none;}
.sidebar a.active{background:rgba(255,255,255,.25);}
.main{margin-left:260px;padding:25px;}
.stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(200px,1fr));gap:20px;}
.card{background:white;padding:18px;border-radius:16px;box-shadow:0 5px 15px rgba(0,0,0,.05);}
.grid{display:grid;grid-template-columns:2fr 1fr;gap:20px;margin-top:20px;}
</style>
</head>

<body>

<div class="sidebar">
<h2>🐾 ARSS</h2>
<a href="dashboard.php">Dashboard</a>
<a href="reports.php">Reports</a>
<a href="events.php" class="active">Events</a>
<a href="logout.php">Logout</a>
</div>

<div class="main">

<h1>Events Dashboard</h1>

<div class="stats">
<div class="card"><h4>Total Events</h4><h2><?= $total ?></h2></div>
</div>

<div class="grid">

<div>

<div class="card" style="margin-bottom:20px;">
<h3>Add Event</h3>
<form method="POST">
<input name="title" placeholder="Title"><br>
<input name="location" placeholder="Location"><br>
<input type="date" name="date"><br>
<textarea name="description"></textarea><br>
<button>Add</button>
</form>
</div>

<?php foreach($events as $e): ?>
<div class="card" style="margin-bottom:15px;">
<h3><?= $e['title'] ?></h3>
<p><?= $e['location'] ?></p>
<p><?= $e['date'] ?></p>
<p><?= $e['description'] ?></p>
<a href="?delete=<?= $e['_id'] ?>">Delete</a>
</div>
<?php endforeach; ?>

</div>

<div class="card">
<h3>Overview</h3>
<p>Total Events: <?= $total ?></p>
</div>

</div>

</div>
</body>
</html>
