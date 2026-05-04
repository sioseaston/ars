<?php 
require '../db.php';

// COUNTS
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

// 🔥 EVENTS (for Latest News)
$events = $db->events->find(
    [],
    ['sort' => ['_id' => -1], 'limit' => 2]
);
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard - ARS</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="../assets/css/dashboard.css">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
.animal-board {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 20px;
    margin-top: 25px;
}

.animal-panel {
    background: white;
    padding: 20px;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.animal-panel h3 {
    margin-top: 0;
    color: #1b4332;
}

.animal-item {
    display: flex;
    gap: 12px;
    padding: 12px 0;
    border-top: 1px solid #edf2ef;
}

.animal-item img {
    width: 78px;
    height: 78px;
    border-radius: 10px;
    object-fit: cover;
}

.animal-item strong {
    display: block;
    color: #1b4332;
}

.animal-item p {
    margin: 4px 0;
    color: #555;
}

.empty-note {
    background: #f4f6f5;
    border-radius: 10px;
    padding: 14px;
    color: #555;
}

@media (max-width: 1100px) {
    .animal-board {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media (max-width: 768px) {
    .animal-board {
        grid-template-columns: 1fr;
    }
}
</style>
</head>
<body>

<div class="layout">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2><i class="fa-solid fa-paw"></i> ARS</h2>

        <a href="dashboard.php" class="active"><i class="fa-solid fa-house"></i> Dashboard</a>
        <a href="report.php"><i class="fa-solid fa-flag"></i> Report</a>
        <a href="surrender.php"><i class="fa-solid fa-hand"></i> Surrender</a>
        <a href="events.php"><i class="fa-solid fa-calendar"></i> Events</a>
        <a href="resources.php"><i class="fa-solid fa-book"></i> Resources</a>
        <a href="about.php"><i class="fa-solid fa-circle-info"></i> About</a>
    </div>

    <!-- MAIN -->
    <div class="main">
        <div class="container">

            <!-- HEADER -->
            <div class="header">
                <h1>Animal Rescue System</h1>
            </div>

            <!-- HERO -->
            <section class="hero">
                <div class="overlay">
                    <h2>Report Domestic and Wildlife Animals</h2>
                    <p>Help report missing or found domestic animals, wildlife near homes, and wildlife in critical condition.</p>
                </div>
            </section>

            <!-- STATS -->
            <section class="stats">

                <div class="card">
                    <div class="icon"><i class="fa-solid fa-paw"></i></div>
                    <div>
                        <h3><?= $rescued ?></h3>
                        <p>Animals Helped</p>
                    </div>
                </div>

                <div class="card">
                    <div class="icon"><i class="fa-solid fa-magnifying-glass"></i></div>
                    <div>
                        <h3><?= $missingReports ?></h3>
                        <p>Missing Reports</p>
                    </div>
                </div>

                <div class="card">
                    <div class="icon"><i class="fa-solid fa-location-dot"></i></div>
                    <div>
                        <h3><?= $foundReports ?></h3>
                        <p>Found Reports</p>
                    </div>
                </div>

                <div class="card">
                    <div class="icon"><i class="fa-solid fa-tree"></i></div>
                    <div>
                        <h3><?= $wildlifeReports ?></h3>
                        <p>Wildlife Reports</p>
                    </div>
                </div>

                <div class="card">
                    <div class="icon"><i class="fa-solid fa-clipboard"></i></div>
                    <div>
                        <h3><?= $pendingReports ?></h3>
                        <p>Pending Cases</p>
                    </div>
                </div>

            </section>

            <!-- MISSING AND FOUND -->
            <section class="animal-board">

                <div class="animal-panel">
                    <h3>Reported Missing Domestic Animals</h3>

                    <?php
                    $hasMissing = false;
                    foreach ($missingAnimals as $animal):
                        $hasMissing = true;
                    ?>
                        <div class="animal-item">
                            <img src="<?= htmlspecialchars($animal['image'] ?? '../assets/images/bg.jpg') ?>" alt="missing animal">
                            <div>
                                <strong><?= htmlspecialchars($animal['animal'] ?? 'Domestic Animal') ?></strong>
                                <p><?= htmlspecialchars($animal['location'] ?? 'Location not provided') ?></p>
                                <p><?= htmlspecialchars($animal['description'] ?? '') ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (!$hasMissing): ?>
                        <div class="empty-note">No approved missing animal reports yet.</div>
                    <?php endif; ?>
                </div>

                <div class="animal-panel">
                    <h3>Domestic Animals Found by the Community</h3>

                    <?php
                    $hasFound = false;
                    foreach ($foundAnimals as $animal):
                        $hasFound = true;
                    ?>
                        <div class="animal-item">
                            <img src="<?= htmlspecialchars($animal['image'] ?? '../assets/images/bg.jpg') ?>" alt="found animal">
                            <div>
                                <strong><?= htmlspecialchars($animal['animal'] ?? 'Domestic Animal') ?></strong>
                                <p><?= htmlspecialchars($animal['location'] ?? 'Location not provided') ?></p>
                                <p><?= htmlspecialchars($animal['description'] ?? '') ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (!$hasFound): ?>
                        <div class="empty-note">No approved found animal reports yet.</div>
                    <?php endif; ?>
                </div>

                <div class="animal-panel">
                    <h3>Wildlife Reports</h3>

                    <?php
                    $hasWildlife = false;
                    foreach ($wildlifeAnimals as $animal):
                        $hasWildlife = true;
                        $wildlifeStatus = ($animal['report_type'] ?? '') === 'wildlife_critical' ? 'Critical condition' : 'Found near home/community';
                    ?>
                        <div class="animal-item">
                            <img src="<?= htmlspecialchars($animal['image'] ?? '../assets/images/bg.jpg') ?>" alt="wildlife animal">
                            <div>
                                <strong><?= htmlspecialchars($animal['animal'] ?? 'Wildlife Animal') ?></strong>
                                <p><?= htmlspecialchars($wildlifeStatus) ?></p>
                                <p><?= htmlspecialchars($animal['location'] ?? 'Location not provided') ?></p>
                                <p><?= htmlspecialchars($animal['description'] ?? '') ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>

                    <?php if (!$hasWildlife): ?>
                        <div class="empty-note">No approved wildlife reports yet.</div>
                    <?php endif; ?>
                </div>

            </section>

            <!-- FOOTER GRID -->
            <section class="footer-grid">

                <!-- ABOUT -->
                <div class="footer-box">
                    <h3>About ARS</h3>
                    <p>
                        The Animal Rescue System (ARS) helps the community report domestic animals, wildlife found near homes, wildlife in critical condition, and wildlife surrender cases. Our goal is to connect reports with responders and help animals return to safe care.
                    </p>
                    <a href="about.php" class="learn-more">Learn more about us -></a>
                </div>

                <!-- NEWS -->
                <div class="footer-box">
                    <div class="title-row">
                        <h3>Latest News & Events</h3>
                        <a href="events.php">View All</a>
                    </div>

                    <?php foreach ($events as $event): ?>
                        <div class="news-item">
                            <img src="../assets/images/bg.jpg" alt="event">
                            <div>
                                <strong><?= htmlspecialchars($event['title'] ?? 'Event') ?></strong>
                                <p><?= htmlspecialchars($event['description'] ?? '') ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- LINKS -->
                <div class="footer-box">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="report.php">Report Missing Domestic Animal</a></li>
                        <li><a href="report.php">Report Found Domestic Animal</a></li>
                        <li><a href="report.php">Report Wildlife Animal</a></li>
                        <li><a href="surrender.php">Surrender Wildlife Animal</a></li>
                        <li><a href="resources.php">Care Resources</a></li>
                        <li><a href="about.php">Contact Us</a></li>
                    </ul>
                </div>

                <!-- CONTACT -->
                <div class="footer-box">
                    <h3>Stay Connected</h3>
                    <p>Follow us for domestic and wildlife animal updates.</p>

                    <div class="socials">
                        <i class="fa-brands fa-facebook"></i>
                        <i class="fa-brands fa-instagram"></i>
                        <i class="fa-brands fa-twitter"></i>
                        <i class="fa-brands fa-youtube"></i>
                    </div>

                    <div class="hotline">
                        <p><strong>Contact No</strong></p>
                        <p>0945 889 8099</p>
                        <p>0992 673 8491</p>

                        <!-- ✅ EMAIL ADDED -->
                        <p><strong>Email</strong></p>
                        <p>pwrcc.pawb@gmail.com</p>
                    </div>
                </div>

            </section>

        </div>
    </div>
</div>

</body>
</html>
