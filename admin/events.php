<?php
require '../includes/auth.php';
require '../db.php';

$collection = $db->events;

// ADD EVENT
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $collection->insertOne([
        'title' => $_POST['title'],
        'location' => $_POST['location'],
        'date' => $_POST['date'],
        'description' => $_POST['description'],
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ]);
}

// DELETE EVENT
if (isset($_GET['delete'])) {
    $collection->deleteOne([
        '_id' => new MongoDB\BSON\ObjectId($_GET['delete'])
    ]);
}

$events = $collection->find([], ['sort' => ['_id' => -1]]);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Events</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>

/* ===== SAME AS ADMIN DASHBOARD ===== */
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
    padding:20px 20px 15px;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    position:fixed;
    top:0;
    left:0;
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

.logout{
    padding:12px;
    border-radius:12px;
    background:rgba(255,255,255,0.1);
    text-align:center;
    text-decoration:none;
    color:white;
}

.logout:hover{
    background:rgba(255,255,255,0.25);
}

/* MAIN */
.main{
    margin-left:260px;
    padding:20px;
}

.container{
    max-width:1100px;
    margin:auto;
}

/* FORM + BOX */
.box{
    background:white;
    padding:15px;
    border-radius:12px;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
}

input, textarea{
    width:100%;
    padding:10px;
    margin:5px 0;
    border-radius:8px;
    border:1px solid #ccc;
}

button{
    background:#2d6a4f;
    color:white;
    border:none;
    padding:10px;
    border-radius:8px;
    cursor:pointer;
}

</style>
</head>

<body>

<!-- SIDEBAR (IDENTICAL) -->
<div class="sidebar">
    <div>

        <div class="logo">
            <i class="fas fa-paw"></i> ARS
        </div>

        <div class="menu">
            <a href="dashboard.php"><i class="fas fa-chart-bar"></i> Dashboard</a>
            <a href="reports.php"><i class="fas fa-flag"></i> Manage Reports</a>
            <a href="surrenders.php"><i class="fas fa-box"></i> Manage Surrenders</a>
            <a href="adoptions.php"><i class="fas fa-heart"></i> Manage Adoptions</a>
            <a href="events.php" class="active"><i class="fas fa-calendar"></i> Manage Events</a>
        </div>

    </div>

    <a href="logout.php" class="logout">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</div>

<!-- MAIN -->
<div class="main">
<div class="container">

<h2>📅 Manage Events</h2>

<form method="POST" class="box" style="margin-bottom:20px;">
    <input type="text" name="title" placeholder="Event Title" required>
    <input type="text" name="location" placeholder="Location" required>
    <input type="date" name="date" required>
    <textarea name="description" placeholder="Description" required></textarea>
    <button type="submit">Add Event</button>
</form>

<?php foreach ($events as $e): ?>
<div class="box" style="margin-bottom:15px;">
    <h3><?= $e['title'] ?></h3>
    <p>📍 <?= $e['location'] ?></p>
    <p>📅 <?= $e['date'] ?></p>
    <p><?= $e['description'] ?></p>

    <a href="?delete=<?= $e['_id'] ?>" style="color:red;">Delete</a>
</div>
<?php endforeach; ?>

</div>
</div>

</body>
</html>