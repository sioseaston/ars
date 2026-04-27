<?php 
require '../db.php';

// COUNTS
$rescued = $db->reports->countDocuments(['status' => 'approved']);
$adopted = $db->adoptions->countDocuments(['status' => 'approved']);
$reports = $db->reports->countDocuments(['status' => 'pending']);
$surrenders = $db->surrenders->countDocuments(['status' => 'pending']);

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
</head>
<body>

<div class="layout">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2><i class="fa-solid fa-paw"></i> ARS</h2>

        <a href="dashboard.php" class="active"><i class="fa-solid fa-house"></i> Dashboard</a>
        <a href="report.php"><i class="fa-solid fa-flag"></i> Report</a>
        <a href="surrender.php"><i class="fa-solid fa-hand"></i> Surrender</a>
        <a href="adopt.php"><i class="fa-solid fa-heart"></i> Adopt</a>
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
                    <h2>Together, We Can Save More Lives</h2>
                    <p>Help animals by reporting, surrendering, or adopting responsibly.</p>
                </div>
            </section>

            <!-- STATS -->
            <section class="stats">

                <div class="card">
                    <div class="icon"><i class="fa-solid fa-paw"></i></div>
                    <div>
                        <h3><?= $rescued ?></h3>
                        <p>Animals Rescued</p>
                    </div>
                </div>

                <div class="card">
                    <div class="icon"><i class="fa-solid fa-heart"></i></div>
                    <div>
                        <h3><?= $adopted ?></h3>
                        <p>Animals Adopted</p>
                    </div>
                </div>

                <div class="card">
                    <div class="icon"><i class="fa-solid fa-clipboard"></i></div>
                    <div>
                        <h3><?= $reports ?></h3>
                        <p>Active Reports</p>
                    </div>
                </div>

                <div class="card">
                    <div class="icon"><i class="fa-solid fa-user"></i></div>
                    <div>
                        <h3><?= $surrenders ?></h3>
                        <p>Surrenders</p>
                    </div>
                </div>

            </section>

            <!-- FOOTER GRID -->
            <section class="footer-grid">

                <!-- ABOUT -->
                <div class="footer-box">
                    <h3>About ARS</h3>
                    <p>
                        The Animal Rescue System (ARS) is dedicated to the rescue,
                        rehabilitation, and rehoming of animals in need. Together with our
                        partners and community, we strive to build a humane and compassionate
                        society for all animals.
                    </p>
                    <a href="about.php" class="learn-more">Learn more about us →</a>
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
                        <li><a href="#">Impact Reports</a></li>
                        <li><a href="#">Team</a></li>
                        <li><a href="#">Gallery</a></li>
                        <li><a href="#">FAQ</a></li>
                        <li><a href="#">Contact Us</a></li>
                    </ul>
                </div>

                <!-- CONTACT -->
                <div class="footer-box">
                    <h3>Stay Connected</h3>
                    <p>Follow us for updates and animal stories.</p>

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