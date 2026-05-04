<?php
require '../includes/auth.php';
require '../db.php';

$collection = $db->reports;

if (isset($_GET['action']) && isset($_GET['id'])) {

    $id = new MongoDB\BSON\ObjectId($_GET['id']);

    if ($_GET['action'] == 'approve') {
        $collection->updateOne(['_id' => $id], ['$set' => ['status' => 'approved']]);
    }

    if ($_GET['action'] == 'reject') {
        $collection->updateOne(['_id' => $id], ['$set' => ['status' => 'rejected']]);
    }

    header("Location: reports.php");
    exit;
}

$reports = $collection->find([], ['sort' => ['created_at' => -1]]);
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Reports</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<style>

/* ===== GLOBAL ===== */
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
    padding:20px;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    position:fixed;
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
}

.menu a:hover{
    background:rgba(255,255,255,0.15);
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

/* MAIN */
.main{
    margin-left:260px;
    padding:25px;
}

.container{
    max-width:1100px;
    margin:auto;
}

/* HEADER */
.header{
    display:flex;
    justify-content:space-between;
    align-items:center;
    margin-bottom:20px;
}

.top-actions{
    display:flex;
    gap:10px;
}

.search{
    padding:10px;
    border-radius:8px;
    border:1px solid #ddd;
}

.export{
    background:#2d6a4f;
    color:white;
    padding:10px 15px;
    border:none;
    border-radius:8px;
    cursor:pointer;
}

/* REPORT CARD */
.report{
    display:flex;
    gap:15px;
    background:white;
    padding:15px;
    border-radius:14px;
    margin-bottom:15px;
    box-shadow:0 5px 15px rgba(0,0,0,0.05);
    align-items:center;
    transition:0.2s;
}

.report:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 25px rgba(0,0,0,0.1);
}

.report img{
    width:200px;
    height:130px;
    object-fit:cover;
    border-radius:10px;
    cursor:pointer;
    transition:0.2s;
}

.report img:hover{
    transform:scale(1.05);
}

.details{
    flex:1;
}

.details h3{
    margin:0;
    color:#2d6a4f;
}

.meta{
    font-size:14px;
    color:#666;
}

/* MAP */
.map{
    width:250px;
    height:120px;
    border-radius:10px;
    cursor:pointer;
}

/* STATUS */
.badge{
    padding:6px 12px;
    border-radius:20px;
    font-size:12px;
    font-weight:bold;
}

.pending{ background:#fff3cd; color:#856404; }
.approved{ background:#d4edda; color:#155724; }
.rejected{ background:#f8d7da; color:#721c24; }

/* ACTIONS */
.actions{
    display:flex;
    flex-direction:column;
    gap:8px;
}

.btn{
    padding:8px 12px;
    border-radius:6px;
    color:white;
    text-decoration:none;
    text-align:center;
}

.approve{ background:#2d6a4f; }
.reject{ background:#d00000; }

/* ===== MODAL ===== */
.modal{
    display:none;
    position:fixed;
    z-index:999;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.85);
}

.modal-content{
    display:block;
    margin:60px auto;
    max-width:80%;
    max-height:70vh;
    border-radius:10px;
}

#modalMap{
    width:80%;
    height:400px;
    margin:60px auto;
    border-radius:10px;
}

.closeBtn{
    position:absolute;
    top:20px;
    right:40px;
    font-size:35px;
    color:white;
    cursor:pointer;
}

</style>
</head>

<body>

<!-- MODAL -->
<div id="previewModal" class="modal">
    <span class="closeBtn">&times;</span>
    <img id="modalImage" class="modal-content">
    <div id="modalMap"></div>
</div>

<!-- SIDEBAR -->
<div class="sidebar">
    <div>
        <div class="logo"><i class="fas fa-paw"></i> ARS</div>

        <div class="menu">
            <a href="dashboard.php"><i class="fas fa-chart-bar"></i> Dashboard</a>
            <a href="reports.php" class="active"><i class="fas fa-flag"></i> Manage Reports</a>
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

<div class="header">
    <div>
        <h2>Animal Reports</h2>
        <small>Review and manage animal reports submitted by the community.</small>
    </div>

    <div class="top-actions">
        <input type="text" class="search" placeholder="Search reports...">
        <button class="export">Export Reports</button>
    </div>
</div>

<?php foreach ($reports as $report): ?>
<div class="report">

    <img src="<?= $report['image'] ?>" 
         onclick="openImage('<?= $report['image'] ?>')">

    <div class="details">
        <h3><?= $report['animal'] ?></h3>

        <div class="meta">
            Name: <?= $report['name'] ?> |
            Contact: <?= $report['contact'] ?> |
            Category: <?= ucwords(str_replace('_', ' ', $report['animal_category'] ?? 'domestic')) ?> |
            Report: <?= ucwords(str_replace('_', ' ', $report['report_type'] ?? 'general')) ?> |
            Location: <?= $report['location'] ?>
        </div>

        <p><?= $report['description'] ?></p>

        <span class="badge <?= $report['status'] ?>">
            <?= strtoupper($report['status']) ?>
        </span>
    </div>

    <div class="map"
         onclick="openMap(<?= $report['latitude'] ?>, <?= $report['longitude'] ?>)"></div>

    <div class="actions">
        <a href="?action=approve&id=<?= $report['_id'] ?>" class="btn approve">Approve</a>
        <a href="?action=reject&id=<?= $report['_id'] ?>" class="btn reject">Reject</a>
    </div>

</div>

<script>
var map<?= $report['_id'] ?> = L.map(document.querySelectorAll('.map')[<?= $loopIndex ?? 0 ?>]).setView(
    [<?= $report['latitude'] ?>, <?= $report['longitude'] ?>], 15
);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png')
.addTo(map<?= $report['_id'] ?>);

L.marker([<?= $report['latitude'] ?>, <?= $report['longitude'] ?>])
.addTo(map<?= $report['_id'] ?>);
</script>

<?php endforeach; ?>

</div>
</div>

<script>
const modal = document.getElementById("previewModal");
const modalImg = document.getElementById("modalImage");
const modalMap = document.getElementById("modalMap");

function openImage(src){
    modal.style.display = "block";
    modalImg.style.display = "block";
    modalMap.style.display = "none";
    modalImg.src = src;
}

function openMap(lat, lng){
    modal.style.display = "block";
    modalImg.style.display = "none";
    modalMap.style.display = "block";

    modalMap.innerHTML = "";

    let map = L.map('modalMap').setView([lat, lng], 16);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png')
    .addTo(map);

    L.marker([lat, lng]).addTo(map);
}

document.querySelector(".closeBtn").onclick = () => modal.style.display="none";
window.onclick = (e)=>{ if(e.target==modal) modal.style.display="none"; }
</script>

</body>
</html>
