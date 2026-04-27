<?php
require '../includes/auth.php';
require '../db.php';

$collection = $db->surrenders;

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

    header("Location: surrenders.php");
    exit;
}

$surrenders = $collection->find([], ['sort' => ['created_at' => -1]]);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Surrenders</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<!-- ICONS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

<!-- LEAFLET -->
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<style>

/* ===== ADMIN DASHBOARD STYLE ===== */
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

/* CARD */
.card{
    background:white;
    padding:18px;
    border-radius:14px;
    margin-bottom:15px;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
}

/* IMAGE */
img{
    width:100%;
    max-height:220px;
    object-fit:cover;
    border-radius:10px;
}

/* MAP */
.map{
    height:180px;
    margin-top:10px;
    border-radius:10px;
}

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

.status{
    font-weight:bold;
}

.pending{color:orange;}
.approved{color:green;}
.rejected{color:red;}

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
            <a href="surrenders.php" class="active"><i class="fas fa-box"></i> Manage Surrenders</a>
            <a href="adoptions.php"><i class="fas fa-heart"></i> Manage Adoptions</a>
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

<h2>🐾 Surrender Requests</h2>

<?php foreach ($surrenders as $s): ?>
<div class="card">

    <h3><?= $s['animal'] ?? 'Unknown Animal' ?></h3>

    <p><strong>Owner:</strong> <?= $s['owner'] ?? 'N/A' ?></p>
    <p><strong>Contact:</strong> <?= $s['contact'] ?? 'N/A' ?></p>

    <p><strong>Location:</strong> <?= $s['location'] ?? 'No location provided' ?></p>

    <p><strong>Reason:</strong> <?= $s['reason'] ?? 'N/A' ?></p>

    <?php if (!empty($s['image'])): ?>
        <img src="<?= $s['image'] ?>">
    <?php endif; ?>

    <?php if (isset($s['latitude']) && isset($s['longitude'])): ?>
        <div id="map<?= $s['_id'] ?>" class="map"></div>

        <script>
        var map<?= $s['_id'] ?> = L.map('map<?= $s['_id'] ?>').setView(
            [<?= $s['latitude'] ?>, <?= $s['longitude'] ?>], 15
        );

        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png')
            .addTo(map<?= $s['_id'] ?>);

        L.marker([<?= $s['latitude'] ?>, <?= $s['longitude'] ?>])
            .addTo(map<?= $s['_id'] ?>);
        </script>
    <?php else: ?>
        <p><em>No map data available</em></p>
    <?php endif; ?>

    <p class="status <?= $s['status'] ?? 'pending' ?>">
        <?= strtoupper($s['status'] ?? 'pending') ?>
    </p>

    <div class="actions">
        <a href="?action=approve&id=<?= $s['_id'] ?>" class="btn approve">Approve</a>
        <a href="?action=reject&id=<?= $s['_id'] ?>" class="btn reject">Reject</a>
    </div>

</div>
<?php endforeach; ?>

</div>
</div>

</body>
</html>