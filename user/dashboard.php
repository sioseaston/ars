<?php 
require '../db.php';

// COUNTS
$rescued = $db->reports->countDocuments(['status' => 'approved']);
$missingReports = $db->reports->countDocuments(['report_type' => 'missing']);
$foundReports = $db->reports->countDocuments(['report_type' => 'found']);
$wildlifeReports = $db->reports->countDocuments(['animal_category' => 'wildlife']);
// Note: Pending reports count is kept in PHP but removed from HTML to match the 4-card UI design.
$pendingReports = $db->reports->countDocuments(['status' => 'pending']); 

$missingAnimals = $db->reports->find(
    ['status' => 'approved', 'animal_category' => ['$ne' => 'wildlife'], 'report_type' => 'missing'],
    ['sort' => ['created_at' => -1], 'limit' => 5] // Adjusted limit to match design
);

$foundAnimals = $db->reports->find(
    ['status' => 'approved', 'animal_category' => ['$ne' => 'wildlife'], 'report_type' => 'found'],
    ['sort' => ['created_at' => -1], 'limit' => 5]
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
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --sidebar-bg: #0a3a2a;
        --sidebar-hover: #164f3b;
        --bg-color: #f7f9f8;
        --text-main: #1f2937;
        --text-muted: #6b7280;
        --card-bg: #ffffff;
        --border-color: #f3f4f6;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
    
    body { background-color: var(--bg-color); color: var(--text-main); display: flex; min-height: 100vh; }

    /* SIDEBAR */
    .sidebar { width: 260px; background-color: var(--sidebar-bg); color: white; display: flex; flex-direction: column; padding: 24px 16px; position: fixed; height: 100vh; }
    .sidebar-brand { display: flex; align-items: center; gap: 12px; margin-bottom: 40px; padding: 0 10px; }
    .sidebar-brand .logo { background: white; color: var(--sidebar-bg); width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .sidebar-brand h2 { font-size: 20px; font-weight: 700; line-height: 1.1; margin: 0; }
    .sidebar-brand span { font-size: 11px; font-weight: 400; opacity: 0.8; }
    
    .sidebar-nav { display: flex; flex-direction: column; gap: 8px; flex: 1; }
    .sidebar-nav a { color: white; text-decoration: none; display: flex; align-items: center; gap: 12px; padding: 12px 16px; border-radius: 8px; font-size: 14px; font-weight: 500; transition: background 0.2s; }
    .sidebar-nav a:hover { background-color: var(--sidebar-hover); }
    .sidebar-nav a.active { background-color: rgba(255, 255, 255, 0.15); font-weight: 600; }
    .sidebar-nav a i { width: 20px; font-size: 16px; opacity: 0.9; }

    .sidebar-bottom { margin-top: auto; background: rgba(0,0,0,0.15); padding: 20px; border-radius: 12px; text-align: center; border: 1px solid rgba(255,255,255,0.05); }
    .sidebar-bottom i { font-size: 32px; color: #4ade80; opacity: 0.8; margin-bottom: 12px; }
    .sidebar-bottom p { font-size: 13px; line-height: 1.4; font-weight: 500; }

    /* MAIN CONTENT */
    .main { margin-left: 260px; padding: 24px 32px; flex: 1; max-width: 1400px; }

    /* HERO */
    .hero { background: linear-gradient(to right, #0a3a2a 40%, rgba(10, 58, 42, 0.4)), url('../assets/images/hero-bg.jpg') center/cover; border-radius: 16px; padding: 40px; color: white; position: relative; overflow: hidden; margin-bottom: 24px; box-shadow: 0 4px 12px rgba(0,0,0,0.05); }
    .hero h2 { font-size: 28px; margin-bottom: 10px; font-weight: 600; }
    .hero p { font-size: 14px; opacity: 0.9; max-width: 400px; line-height: 1.5; }
    .hero img { position: absolute; right: 20px; bottom: 0; height: 110%; object-fit: contain; }

    /* STATS */
    .stats { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 24px; }
    .stat-card { background: var(--card-bg); padding: 20px; border-radius: 12px; display: flex; align-items: center; gap: 16px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .stat-icon { width: 48px; height: 48px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 20px; }
    .icon-green { background: #e6f4ea; color: #1e8e3e; }
    .icon-blue { background: #e8f0fe; color: #1a73e8; }
    .icon-teal { background: #e0f2f1; color: #00897b; }
    .stat-info h3 { font-size: 24px; font-weight: 700; margin: 0; color: var(--text-main); }
    .stat-info p { font-size: 13px; color: var(--text-muted); margin: 0; font-weight: 500; }

    /* DATA PANELS */
    .tables-container { display: grid; grid-template-columns: 1fr 1fr; gap: 24px; margin-bottom: 24px; }
    .panel { background: var(--card-bg); border-radius: 12px; padding: 24px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .panel-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; }
    .panel-header h3 { font-size: 16px; font-weight: 600; color: var(--text-main); }
    .panel-header a { font-size: 13px; color: #1a73e8; text-decoration: none; font-weight: 500; }

    .data-table { width: 100%; border-collapse: collapse; }
    .data-table th { text-align: left; padding-bottom: 12px; font-size: 12px; color: var(--text-muted); font-weight: 500; border-bottom: 1px solid var(--border-color); }
    .data-table td { padding: 12px 0; border-bottom: 1px solid var(--border-color); vertical-align: middle; }
    .data-table tr:last-child td { border-bottom: none; }
    
    .cell-animal { display: flex; align-items: center; gap: 12px; }
    .cell-animal img { width: 36px; height: 36px; border-radius: 8px; object-fit: cover; }
    .cell-info strong { display: block; font-size: 14px; color: var(--text-main); }
    .cell-info span { font-size: 12px; color: var(--text-muted); }
    
    .cell-location { display: flex; align-items: flex-start; gap: 8px; }
    .cell-location i { color: var(--text-muted); font-size: 14px; margin-top: 2px; }

    .status-pill { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    .status-pending { background: #fff3e0; color: #e65100; }
    .status-found { background: #e6f4ea; color: #1e8e3e; }

    /* FOOTER GRID */
    .footer-grid { display: grid; grid-template-columns: 1fr 1.2fr 1.5fr 1fr; gap: 24px; }
    .footer-box { background: var(--card-bg); padding: 24px; border-radius: 12px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
    .footer-box h3 { font-size: 14px; font-weight: 600; margin-bottom: 16px; color: var(--text-main); }
    .footer-box p { font-size: 13px; color: var(--text-muted); line-height: 1.5; }
    
    /* About Box */
    .about-header { display: flex; align-items: center; gap: 12px; margin-bottom: 12px; }
    .about-icon { width: 40px; height: 40px; background: #e6f4ea; color: #1e8e3e; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 18px; }

    /* News Box */
    .news-item { display: flex; gap: 12px; margin-bottom: 16px; }
    .news-item:last-child { margin-bottom: 0; }
    .news-item img { width: 50px; height: 50px; border-radius: 8px; object-fit: cover; }
    .news-item-info { flex: 1; }
    .news-item-info strong { display: block; font-size: 13px; margin-bottom: 4px; }
    .news-meta { display: flex; justify-content: space-between; align-items: center; font-size: 11px; color: var(--text-muted); }
    .news-tag { padding: 2px 8px; border-radius: 4px; background: #e8f0fe; color: #1a73e8; }

    /* Quick Links Box */
    .links-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .link-btn { display: flex; align-items: center; justify-content: space-between; padding: 12px; border: 1px solid var(--border-color); border-radius: 8px; text-decoration: none; color: var(--text-main); font-size: 12px; font-weight: 500; transition: border-color 0.2s; }
    .link-btn:hover { border-color: var(--sidebar-bg); }
    .link-btn .left-side { display: flex; align-items: center; gap: 10px; }
    .link-btn .left-side i { font-size: 16px; color: #1e8e3e; }
    .link-btn .fa-chevron-right { font-size: 10px; color: var(--text-muted); }

    /* Stay Connected */
    .social-icons { display: flex; gap: 12px; margin-top: 16px; }
    .social-icons a { display: flex; align-items: center; justify-content: center; width: 32px; height: 32px; border-radius: 50%; color: white; text-decoration: none; font-size: 14px; }
    .bg-fb { background: #1877F2; }
    .bg-ig { background: linear-gradient(45deg, #f09433 0%, #e6683c 25%, #dc2743 50%, #cc2366 75%, #bc1888 100%); }
    .bg-tw { background: #1DA1F2; }
    .bg-yt { background: #FF0000; }

    .copyright { text-align: center; font-size: 12px; color: var(--text-muted); margin-top: 24px; padding-bottom: 24px; }
</style>
</head>
<body>

    <div class="sidebar">
        <div class="sidebar-brand">
            <div class="logo"><i class="fa-solid fa-paw"></i></div>
            <div>
                <h2>ARS</h2>
                <span>Animal Rescue System</span>
            </div>
        </div>

        <nav class="sidebar-nav">
            <a href="dashboard.php" class="active"><i class="fa-solid fa-house"></i> Dashboard</a>
            <a href="report.php"><i class="fa-solid fa-flag"></i> Report</a>
            <a href="surrender.php"><i class="fa-solid fa-hand"></i> Surrender</a>
            <a href="events.php"><i class="fa-solid fa-calendar"></i> Events</a>
            <a href="resources.php"><i class="fa-solid fa-book"></i> Resources</a>
            <a href="about.php"><i class="fa-solid fa-circle-info"></i> About</a>
        </nav>

        <div class="sidebar-bottom">
            <i class="fa-solid fa-shield-dog"></i>
            <p>Every rescue makes<br>a difference</p>
        </div>
    </div>

    <div class="main">
        
        <section class="hero">
            <h2>Animal Rescue System</h2>
            <p>Help report missing or found domestic animals, wildlife near homes, and wildlife in critical condition.</p>
            <img src="../assets/images/dog-cat-hero.png" alt="Rescue Animals">
        </section>

        <section class="stats">
            <div class="stat-card">
                <div class="stat-icon icon-green"><i class="fa-solid fa-paw"></i></div>
                <div class="stat-info">
                    <h3><?= $rescued ?></h3>
                    <p>Animals Helped</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-blue"><i class="fa-solid fa-magnifying-glass"></i></div>
                <div class="stat-info">
                    <h3><?= $missingReports ?></h3>
                    <p>Missing Reports</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-teal"><i class="fa-solid fa-location-dot"></i></div>
                <div class="stat-info">
                    <h3><?= $foundReports ?></h3>
                    <p>Found Reports</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon icon-green"><i class="fa-solid fa-tree"></i></div>
                <div class="stat-info">
                    <h3><?= $wildlifeReports ?></h3>
                    <p>Wildlife Reports</p>
                </div>
            </div>
        </section>

        <section class="tables-container">
            <div class="panel">
                <div class="panel-header">
                    <h3>Reported Missing Domestic Animals</h3>
                    <a href="#">View All</a>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Animal</th>
                            <th>Location</th>
                            <th>Status</th>
                            <th>Date Reported</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($missingAnimals as $animal): ?>
                        <tr>
                            <td>
                                <div class="cell-animal">
                                    <img src="<?= htmlspecialchars($animal['image'] ?? '../assets/images/bg.jpg') ?>" alt="animal">
                                    <div class="cell-info">
                                        <strong><?= htmlspecialchars($animal['animal_name'] ?? 'Unknown') ?></strong>
                                        <span><?= htmlspecialchars($animal['breed'] ?? 'Domestic Animal') ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="cell-location">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <div class="cell-info">
                                        <strong><?= htmlspecialchars($animal['location'] ?? 'Location N/A') ?></strong>
                                        <span>Puerto Princesa</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="status-pill status-pending">Pending</span></td>
                            <td>
                                <div class="cell-info">
                                    <?php 
                                        $dateObj = isset($animal['created_at']) ? new DateTime($animal['created_at']) : new DateTime();
                                    ?>
                                    <strong><?= $dateObj->format('M d, Y') ?></strong>
                                    <span><?= $dateObj->format('h:i A') ?></span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="panel">
                <div class="panel-header">
                    <h3>Domestic Animals Found by the Community</h3>
                    <a href="#">View All</a>
                </div>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Animal</th>
                            <th>Location Found</th>
                            <th>Status</th>
                            <th>Date Found</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($foundAnimals as $animal): ?>
                        <tr>
                            <td>
                                <div class="cell-animal">
                                    <img src="<?= htmlspecialchars($animal['image'] ?? '../assets/images/bg.jpg') ?>" alt="animal">
                                    <div class="cell-info">
                                        <strong>Unknown</strong>
                                        <span><?= htmlspecialchars($animal['breed'] ?? 'Mixed Breed') ?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div class="cell-location">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <div class="cell-info">
                                        <strong><?= htmlspecialchars($animal['location'] ?? 'Location N/A') ?></strong>
                                        <span>Puerto Princesa</span>
                                    </div>
                                </div>
                            </td>
                            <td><span class="status-pill status-found">Found</span></td>
                            <td>
                                <div class="cell-info">
                                    <?php 
                                        $dateObj = isset($animal['created_at']) ? new DateTime($animal['created_at']) : new DateTime();
                                    ?>
                                    <strong><?= $dateObj->format('M d, Y') ?></strong>
                                    <span><?= $dateObj->format('h:i A') ?></span>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>

        <section class="footer-grid">
            
            <div class="footer-box">
                <div class="about-header">
                    <div class="about-icon"><i class="fa-solid fa-paw"></i></div>
                    <h3>About ARS</h3>
                </div>
                <p>The Animal Rescue System (ARS) helps the community report domestic animals, wildlife found near homes, wildlife in critical condition, and wildlife surrender cases. Together, we can protect and save more lives.</p>
            </div>

            <div class="footer-box">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h3 style="margin: 0;">Latest News & Events</h3>
                    <a href="#" style="font-size: 12px; color: #1a73e8; text-decoration: none;">View All</a>
                </div>
                
                <?php foreach ($events as $event): ?>
                <div class="news-item">
                    <img src="../assets/images/bg.jpg" alt="event">
                    <div class="news-item-info">
                        <strong><?= htmlspecialchars($event['title'] ?? 'Event Title') ?></strong>
                        <div class="news-meta">
                            <span><i class="fa-regular fa-calendar"></i> May 15, 2025</span>
                            <span class="news-tag">Event</span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="footer-box">
                <h3>Quick Links</h3>
                <div class="links-grid">
                    <a href="report.php" class="link-btn">
                        <div class="left-side">
                            <i class="fa-solid fa-magnifying-glass" style="color: #1e8e3e;"></i>
                            <span>Report Missing<br>Domestic Animal</span>
                        </div>
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                    <a href="report.php" class="link-btn">
                        <div class="left-side">
                            <i class="fa-solid fa-location-dot" style="color: #1e8e3e;"></i>
                            <span>Report Found<br>Domestic Animal</span>
                        </div>
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                    <a href="report.php" class="link-btn">
                        <div class="left-side">
                            <i class="fa-solid fa-tree" style="color: #1e8e3e;"></i>
                            <span>Report Wildlife<br>Animal</span>
                        </div>
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                    <a href="surrender.php" class="link-btn">
                        <div class="left-side">
                            <i class="fa-solid fa-hand-holding-heart" style="color: #1e8e3e;"></i>
                            <span>Surrender<br>Wildlife Animal</span>
                        </div>
                        <i class="fa-solid fa-chevron-right"></i>
                    </a>
                </div>
            </div>

            <div class="footer-box">
                <h3>Stay Connected</h3>
                <p>Follow us for domestic and wildlife animal updates.</p>
                <div class="social-icons">
                    <a href="#" class="bg-fb"><i class="fa-brands fa-facebook-f"></i></a>
                    <a href="#" class="bg-ig"><i class="fa-brands fa-instagram"></i></a>
                    <a href="#" class="bg-tw"><i class="fa-brands fa-twitter"></i></a>
                    <a href="#" class="bg-yt"><i class="fa-brands fa-youtube"></i></a>
                </div>
            </div>

        </section>

        <div class="copyright">
            <i class="fa-solid fa-paw" style="color: #9ca3af; margin-right: 5px;"></i> © 2025 Animal Rescue System (ARS). All rights reserved.
        </div>

    </div>

</body>
</html>
