<?php 
require '../db.php';

// ================= ORIGINAL LOGIC (UNCHANGED) =================
$rescued = $db->reports->countDocuments(['status' => 'approved']);
$missingReports = $db->reports->countDocuments(['report_type' => 'missing']);
$foundReports = $db->reports->countDocuments(['report_type' => 'found']);
$wildlifeReports = $db->reports->countDocuments(['animal_category' => 'wildlife']);
$pendingReports = $db->reports->countDocuments(['status' => 'pending']);

$missingAnimals = $db->reports->find(
    ['status' => 'approved', 'animal_category' => ['$ne' => 'wildlife'], 'report_type' => 'missing'],
    ['sort' => ['created_at' => -1], 'limit' => 3]
);

$foundAnimals = $db->reports->find(
    ['status' => 'approved', 'animal_category' => ['$ne' => 'wildlife'], 'report_type' => 'found'],
    ['sort' => ['created_at' => -1], 'limit' => 3]
);

$wildlifeAnimals = $db->reports->find(
    ['status' => 'approved', 'animal_category' => 'wildlife'],
    ['sort' => ['created_at' => -1], 'limit' => 3]
);

$events = $db->events->find([], ['sort' => ['_id' => -1], 'limit' => 2]);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard - ARS</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/css/dashboard.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">

<style>
body { font-family: 'Inter', sans-serif; }

/* TOPBAR */
.topbar {
    display:flex;
    justify-content:space-between;
    align-items:center;
    background:white;
    padding:15px 25px;
    box-shadow:0 2px 6px rgba(0,0,0,0.05);
    border-radius:10px;
    margin-bottom:20px;
}

/* HERO CLEAN */
.hero-modern {
    background: linear-gradient(135deg,#2d6a4f,#1b4332);
    color:white;
    padding:25px;
    border-radius:15px;
    margin-bottom:20px;
}

/* STATS */
.stats-modern {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(200px,1fr));
    gap:20px;
    margin-bottom:25px;
}

.stat-card {
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 10px rgba(0,0,0,0.05);
    transition:0.2s;
}

.stat-card:hover {
    transform:translateY(-3px);
}

.stat-card h3 {
    margin:0;
    font-size:26px;
}

.stat-card p {
    color:#777;
}

/* PANELS */
.animal-board {
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(300px,1fr));
    gap:20px;
}

.animal-panel {
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 10px rgba(0,0,0,0.05);
}

.animal-item {
    display:flex;
    gap:12px;
    margin-top:15px;
    align-items:center;
}

.animal-item img {
    width:80px;
    height:80px;
    border-radius:10px;
    object-fit:cover;
    cursor:pointer;
}

.animal-item strong {
    display:block;
}

.empty-note {
    margin-top:10px;
    color:#777;
}

/* FOOTER GRID */
.footer-grid {
    margin-top:30px;
    display:grid;
    grid-template-columns:repeat(auto-fit,minmax(250px,1fr));
    gap:20px;
}

.footer-box {
    background:white;
    padding:20px;
    border-radius:12px;
    box-shadow:0 4px 10px rgba(0,0,0,0.05);
}

/* MODAL */
.modal {
    display:none;
    position:fixed;
    background:rgba(0,0,0,0.8);
    top:0; left:0;
    width:100%; height:100%;
    justify-content:center;
    align-items:center;
}

.modal img {
    max-width:90%;
    border-radius:10px;
}
</style>
</head>

<body>

<div class="layout">

<!-- SIDEBAR (UNCHANGED) -->
<div class="sidebar">
    <h2><i class="fa-solid fa-paw"></i> ARS</h2>

    <a href="dashboard.php" class="active"><i class="fa-solid fa-house"></i> Dashboard</a>
    <a href="report.php"><i class="fa-solid fa-flag"></i> Report</a>
    <a href="surrender.php"><i class="fa-solid fa-hand"></i> Surrender</a>
    <a href="events.php"><i class="fa-solid fa-calendar"></i> Events</a>
    <a href="resources.php"><i class="fa-solid fa-book"></i> Resources</a>
    <a href="about.php"><i class="fa-solid fa-circle-info"></i> About</a>
</div>

<div class="main">
<div class="container">

<!-- TOPBAR -->
<div class="topbar">
    <h2>Dashboard</h2>
    <div>👤 Admin</div>
</div>

<!-- HERO -->
<div class="hero-modern">
    <h2>Animal Rescue System</h2>
    <p>Monitor reports, track rescued animals, and manage cases efficiently.</p>
</div>

<!-- STATS -->
<div class="stats-modern">
    <div class="stat-card"><h3><?= $rescued ?></h3><p>Animals Helped</p></div>
    <div class="stat-card"><h3><?= $missingReports ?></h3><p>Missing Reports</p></div>
    <div class="stat-card"><h3><?= $foundReports ?></h3><p>Found Reports</p></div>
    <div class="stat-card"><h3><?= $wildlifeReports ?></h3><p>Wildlife Reports</p></div>
    <div class="stat-card"><h3><?= $pendingReports ?></h3><p>Pending Cases</p></div>
</div>

<!-- PANELS -->
<div class="animal-board">

<!-- MISSING -->
<div class="animal-panel">
<h3>Missing Animals</h3>
<?php $hasMissing=false; foreach($missingAnimals as $a): $hasMissing=true; ?>
<div class="animal-item">
<img src="<?= $a['image'] ?>" onclick="openModal('<?= $a['image'] ?>')">
<div>
<strong><?= $a['animal'] ?></strong>
<p><?= $a['location'] ?></p>
</div>
</div>
<?php endforeach; ?>
<?php if(!$hasMissing): ?><div class="empty-note">No data</div><?php endif; ?>
</div>

<!-- FOUND -->
<div class="animal-panel">
<h3>Found Animals</h3>
<?php $hasFound=false; foreach($foundAnimals as $a): $hasFound=true; ?>
<div class="animal-item">
<img src="<?= $a['image'] ?>" onclick="openModal('<?= $a['image'] ?>')">
<div>
<strong><?= $a['animal'] ?></strong>
<p><?= $a['location'] ?></p>
</div>
</div>
<?php endforeach; ?>
<?php if(!$hasFound): ?><div class="empty-note">No data</div><?php endif; ?>
</div>

<!-- WILDLIFE -->
<div class="animal-panel">
<h3>Wildlife Reports</h3>
<?php $hasWild=false; foreach($wildlifeAnimals as $a): $hasWild=true; ?>
<div class="animal-item">
<img src="<?= $a['image'] ?>" onclick="openModal('<?= $a['image'] ?>')">
<div>
<strong><?= $a['animal'] ?></strong>
<p><?= $a['location'] ?></p>
</div>
</div>
<?php endforeach; ?>
<?php if(!$hasWild): ?><div class="empty-note">No data</div><?php endif; ?>
</div>

</div>

<!-- FOOTER -->
<div class="footer-grid">

<div class="footer-box">
<h3>About ARS</h3>
<p>Community-driven reporting system for domestic and wildlife rescue.</p>
</div>

<div class="footer-box">
<h3>Latest Events</h3>
<?php foreach($events as $e): ?>
<p><strong><?= $e['title'] ?></strong></p>
<?php endforeach; ?>
</div>

<div class="footer-box">
<h3>Quick Links</h3>
<p>Report • Surrender • Resources</p>
</div>

</div>

</div>
</div>
</div>

<!-- MODAL -->
<div class="modal" id="modal" onclick="closeModal()">
<img id="modalImg">
</div>

<script>
function openModal(src){
document.getElementById("modal").style.display="flex";
document.getElementById("modalImg").src=src;
}
function closeModal(){
document.getElementById("modal").style.display="none";
}
</script>

</body>
</html>
