<?php
require '../includes/auth.php';
require '../db.php';

$collection = $db->adoptions;

// HANDLE APPROVE / REJECT
if (isset($_GET['action']) && isset($_GET['id'])) {

    $id = new MongoDB\BSON\ObjectId($_GET['id']);

    if ($_GET['action'] == 'approve') {
        $collection->updateOne(
            ['_id' => $id],
            ['$set' => ['status' => 'approved']]
        );
    }

    if ($_GET['action'] == 'reject') {
        $collection->updateOne(
            ['_id' => $id],
            ['$set' => ['status' => 'rejected']]
        );
    }

    header("Location: adoptions.php");
    exit;
}

$adoptions = $collection->find([], ['sort' => ['created_at' => -1]]);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Adoptions</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- ICONS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<style>

/* ===== SAME ADMIN STYLE ===== */
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
    max-width:1000px;
    margin:auto;
}

/* HEADER */
.header{
    margin-bottom:20px;
}

/* CARD */
.card{
    background:white;
    padding:18px;
    border-radius:14px;
    margin-bottom:15px;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
}

/* STATUS */
.status{
    font-weight:bold;
}

.pending{color:orange;}
.approved{color:green;}
.rejected{color:red;}

/* BUTTONS */
.actions{
    margin-top:10px;
}

.btn{
    padding:8px 12px;
    border-radius:6px;
    color:white;
    text-decoration:none;
    margin-right:5px;
}

.approve{background:#2d6a4f;}
.reject{background:#d00000;}

</style>
</head>

<body>

<!-- SIDEBAR -->
<div class="sidebar">
    <div>

        <div class="logo">
            <i class="fas fa-paw"></i> ARS
        </div>

        <div class="menu">
            <a href="dashboard.php"><i class="fas fa-chart-bar"></i> Dashboard</a>
            <a href="reports.php"><i class="fas fa-flag"></i> Manage Reports</a>
            <a href="surrenders.php"><i class="fas fa-box"></i> Manage Surrenders</a>
            <a href="adoptions.php" class="active"><i class="fas fa-heart"></i> Manage Adoptions</a>
            <a href="events.php"><i class="fas fa-calendar"></i> Manage Events</a>
        </div>

    </div>

    <a href="logout.php" class="logout">
        <i class="fas fa-sign-out-alt"></i> Logout
    </a>
</div>

<!-- MAIN -->
<div class="main">
<div class="container">

<div class="header">
    <h2>❤️ Adoption Requests</h2>
</div>

<?php foreach ($adoptions as $a): ?>
<div class="card">

    <h3><?= $a['animal'] ?></h3>

    <p><strong>Name:</strong> <?= $a['name'] ?></p>
    <p><strong>Contact:</strong> <?= $a['contact'] ?></p>
    <p><strong>Source:</strong> <?= $a['source'] ?></p>

    <p class="status <?= $a['status'] ?>">
        <?= strtoupper($a['status']) ?>
    </p>

    <div class="actions">
        <a href="?action=approve&id=<?= $a['_id'] ?>" class="btn approve">Approve</a>
        <a href="?action=reject&id=<?= $a['_id'] ?>" class="btn reject">Reject</a>
    </div>

</div>
<?php endforeach; ?>

</div>
</div>

</body>
</html>