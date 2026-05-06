<?php
// ---- MOCK DATA (replace with real DB calls) ----
$rescued        = 24;
$missingReports = 12;
$foundReports   = 8;
$wildlifeReports = 5;

$missingAnimals = [
  ['image'=>'https://placedog.net/40/40?id=1','animal_name'=>'Buddy','breed'=>'Golden Retriever','location'=>'Brgy. San Isidro','created_at'=>'2025-05-15 10:30:00'],
  ['image'=>'https://placedog.net/40/40?id=2','animal_name'=>'Ming','breed'=>'Domestic Shorthair','location'=>'Brgy. Tiniguiban','created_at'=>'2025-05-14 03:45:00'],
  ['image'=>'https://placedog.net/40/40?id=3','animal_name'=>'Coco','breed'=>'Shih Tzu','location'=>'Brgy. Bagong Silang','created_at'=>'2025-05-14 09:20:00'],
  ['image'=>'https://placedog.net/40/40?id=4','animal_name'=>'Max','breed'=>'Aspin','location'=>'Brgy. Sicsican','created_at'=>'2025-05-13 06:15:00'],
  ['image'=>'https://placedog.net/40/40?id=5','animal_name'=>'Luna','breed'=>'Tuxedo Cat','location'=>'Brgy. San Pedro','created_at'=>'2025-05-12 11:00:00'],
];

$foundAnimals = [
  ['image'=>'https://placedog.net/40/40?id=6','breed'=>'Mixed Breed','location'=>'Brgy. Bancao-Bancao','created_at'=>'2025-05-15 08:45:00'],
  ['image'=>'https://placedog.net/40/40?id=7','breed'=>'Domestic Cat','location'=>'Brgy. San Miguel','created_at'=>'2025-05-14 02:30:00'],
  ['image'=>'https://placedog.net/40/40?id=8','breed'=>'Poodle Mix','location'=>'Brgy. Mulawin','created_at'=>'2025-05-14 11:10:00'],
  ['image'=>'https://placedog.net/40/40?id=9','breed'=>'Aspin','location'=>'Brgy. San Pedro','created_at'=>'2025-05-13 05:25:00'],
  ['image'=>'https://placedog.net/40/40?id=10','breed'=>'Domestic Cat','location'=>'Brgy. Tiniguiban','created_at'=>'2025-05-12 09:05:00'],
];

$events = [
  ['title'=>'Free Anti-Rabies Vaccination Drive','date'=>'May 15, 2025','tag'=>'Event','image'=>'https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=60&h=60&fit=crop'],
  ['title'=>'Wildlife Awareness Campaign','date'=>'May 10, 2025','tag'=>'News','image'=>'https://images.unsplash.com/photo-1474511320723-9a56873867b5?w=60&h=60&fit=crop'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard - ARS</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
/* ===== RESET & VARIABLES ===== */
*, *::before, *::after { margin:0; padding:0; box-sizing:border-box; }

:root {
  --sidebar-bg:   #0a3a2a;
  --sidebar-hover:#144d38;
  --sidebar-w:    240px;
  --green-dark:   #0a3a2a;
  --green-mid:    #1e8e3e;
  --green-light:  #e6f4ea;
  --blue-light:   #e8f0fe;
  --blue:         #1a73e8;
  --teal-light:   #e0f2f1;
  --teal:         #00897b;
  --orange-light: #fff3e0;
  --orange:       #e65100;
  --bg:           #f4f6f5;
  --card:         #ffffff;
  --text:         #1a2e22;
  --muted:        #6b7280;
  --border:       #eef0ee;
  --radius:       12px;
  --shadow:       0 1px 4px rgba(0,0,0,.07);
  --font:         'DM Sans', sans-serif;
}

body {
  font-family: var(--font);
  background: var(--bg);
  color: var(--text);
  display: flex;
  min-height: 100vh;
}

/* ===== SIDEBAR ===== */
.sidebar {
  width: var(--sidebar-w);
  background: var(--sidebar-bg);
  color: #fff;
  display: flex;
  flex-direction: column;
  padding: 20px 14px 24px;
  position: fixed;
  top: 0; left: 0; bottom: 0;
  z-index: 200;
  transition: transform .3s ease;
}

.sidebar-brand {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 4px 8px 28px;
  border-bottom: 1px solid rgba(255,255,255,.08);
  margin-bottom: 16px;
}
.brand-logo {
  width: 38px; height: 38px;
  background: #fff;
  border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  color: var(--green-dark);
  font-size: 18px;
  flex-shrink: 0;
}
.brand-text { line-height: 1.1; }
.brand-text strong { font-size: 18px; font-weight: 700; display: block; }
.brand-text span { font-size: 10.5px; opacity: .75; font-weight: 400; }

.sidebar-nav { display: flex; flex-direction: column; gap: 4px; flex: 1; }
.sidebar-nav a {
  display: flex; align-items: center; gap: 11px;
  color: rgba(255,255,255,.82);
  text-decoration: none;
  padding: 10px 14px;
  border-radius: 8px;
  font-size: 14px; font-weight: 500;
  transition: background .18s, color .18s;
}
.sidebar-nav a i { width: 18px; font-size: 15px; text-align: center; }
.sidebar-nav a:hover { background: var(--sidebar-hover); color: #fff; }
.sidebar-nav a.active { background: rgba(255,255,255,.15); color: #fff; font-weight: 600; }

.sidebar-footer {
  margin-top: 24px;
  background: rgba(0,0,0,.18);
  border: 1px solid rgba(255,255,255,.06);
  border-radius: 10px;
  padding: 18px 16px;
  text-align: center;
}
.sidebar-footer .paw-icon { font-size: 30px; color: #4ade80; opacity: .85; margin-bottom: 8px; }
.sidebar-footer p { font-size: 12.5px; line-height: 1.45; font-weight: 500; opacity: .9; }

/* Mobile toggle button */
.menu-toggle {
  display: none;
  position: fixed; top: 14px; left: 14px; z-index: 300;
  background: var(--green-dark);
  color: #fff;
  border: none; cursor: pointer;
  width: 38px; height: 38px;
  border-radius: 8px;
  font-size: 16px;
  align-items: center; justify-content: center;
}
.overlay {
  display: none;
  position: fixed; inset: 0;
  background: rgba(0,0,0,.45);
  z-index: 190;
}

/* ===== MAIN CONTENT ===== */
.main {
  margin-left: var(--sidebar-w);
  padding: 24px 28px 40px;
  flex: 1;
  min-width: 0;
}

/* ===== HERO ===== */
.hero {
  background: linear-gradient(100deg, #0a3a2a 45%, rgba(10,58,42,.55) 75%, transparent 100%),
              url('https://images.unsplash.com/photo-1450778869180-41d0601e046e?w=1200&h=280&fit=crop') center/cover no-repeat;
  border-radius: var(--radius);
  padding: 36px 40px;
  color: #fff;
  position: relative;
  overflow: hidden;
  margin-bottom: 22px;
  min-height: 148px;
  display: flex; align-items: center;
}
.hero-text h1 { font-size: 26px; font-weight: 700; margin-bottom: 8px; }
.hero-text p  { font-size: 13.5px; opacity: .88; max-width: 380px; line-height: 1.55; }
.hero-img {
  position: absolute; right: 0; bottom: 0;
  height: 130%;
  object-fit: contain;
  pointer-events: none;
}

/* ===== STAT CARDS ===== */
.stats {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
  margin-bottom: 22px;
}
.stat-card {
  background: var(--card);
  border-radius: var(--radius);
  padding: 18px 20px;
  display: flex; align-items: center; gap: 14px;
  box-shadow: var(--shadow);
}
.stat-icon {
  width: 46px; height: 46px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  font-size: 19px; flex-shrink: 0;
}
.ic-green  { background: var(--green-light); color: var(--green-mid); }
.ic-blue   { background: var(--blue-light);  color: var(--blue); }
.ic-teal   { background: var(--teal-light);  color: var(--teal); }
.stat-num  { font-size: 26px; font-weight: 700; line-height: 1; }
.stat-lbl  { font-size: 12.5px; color: var(--muted); font-weight: 500; margin-top: 2px; }

/* ===== TABLES ===== */
.tables-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 22px;
}
.panel {
  background: var(--card);
  border-radius: var(--radius);
  padding: 20px 22px;
  box-shadow: var(--shadow);
  overflow: hidden;
}
.panel-head {
  display: flex; justify-content: space-between; align-items: center;
  margin-bottom: 14px;
}
.panel-head h2 { font-size: 14px; font-weight: 600; }
.panel-head a  { font-size: 12.5px; color: var(--blue); text-decoration: none; font-weight: 500; }

.tbl { width: 100%; border-collapse: collapse; }
.tbl th {
  text-align: left; font-size: 11.5px; color: var(--muted);
  font-weight: 500; padding-bottom: 10px;
  border-bottom: 1px solid var(--border);
}
.tbl td {
  padding: 10px 0;
  border-bottom: 1px solid var(--border);
  vertical-align: middle;
  font-size: 13px;
}
.tbl tr:last-child td { border-bottom: none; }

.cell-animal { display: flex; align-items: center; gap: 10px; }
.cell-animal img { width: 34px; height: 34px; border-radius: 7px; object-fit: cover; }
.ci-name { font-size: 13px; font-weight: 600; line-height: 1.2; }
.ci-sub  { font-size: 11.5px; color: var(--muted); }

.cell-loc { display: flex; align-items: flex-start; gap: 6px; }
.cell-loc i { color: var(--muted); font-size: 12px; margin-top: 2px; }
.cell-loc .ci-name { font-size: 12.5px; }

.pill {
  display: inline-block; padding: 3px 10px;
  border-radius: 20px; font-size: 11px; font-weight: 600;
}
.pill-pending { background: var(--orange-light); color: var(--orange); }
.pill-found   { background: var(--green-light);  color: var(--green-mid); }

.view-all-row { text-align: center; margin-top: 12px; }
.view-all-row a { font-size: 13px; color: var(--blue); text-decoration: none; font-weight: 500; }

/* ===== FOOTER GRID ===== */
.footer-grid {
  display: grid;
  grid-template-columns: 1fr 1.1fr 1.5fr 1fr;
  gap: 18px;
}
.fbox {
  background: var(--card);
  border-radius: var(--radius);
  padding: 20px 20px;
  box-shadow: var(--shadow);
}
.fbox-title {
  font-size: 13.5px; font-weight: 600; margin-bottom: 12px;
}

/* About box */
.about-head { display: flex; align-items: center; gap: 10px; margin-bottom: 10px; }
.about-icon {
  width: 36px; height: 36px;
  background: var(--green-light); color: var(--green-mid);
  border-radius: 50%; display: flex; align-items: center; justify-content: center;
  font-size: 16px; flex-shrink: 0;
}
.fbox p { font-size: 12.5px; color: var(--muted); line-height: 1.6; }

/* News box */
.news-head { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; }
.news-head h3 { font-size: 13.5px; font-weight: 600; }
.news-head a  { font-size: 12px; color: var(--blue); text-decoration: none; }
.news-item { display: flex; gap: 10px; margin-bottom: 14px; }
.news-item:last-child { margin-bottom: 0; }
.news-item img { width: 48px; height: 48px; border-radius: 8px; object-fit: cover; flex-shrink: 0; }
.ni-title { font-size: 12.5px; font-weight: 600; line-height: 1.35; margin-bottom: 5px; }
.ni-meta  { display: flex; justify-content: space-between; align-items: center; font-size: 11px; color: var(--muted); }
.ni-tag   { padding: 2px 8px; border-radius: 4px; font-size: 10.5px; font-weight: 600; }
.tag-event { background: var(--green-light); color: var(--green-mid); }
.tag-news  { background: var(--blue-light);  color: var(--blue); }

/* Quick links */
.links-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
.lbtn {
  display: flex; align-items: center; justify-content: space-between;
  padding: 11px 12px;
  border: 1.5px solid var(--border);
  border-radius: 9px;
  text-decoration: none; color: var(--text);
  font-size: 11.5px; font-weight: 500;
  transition: border-color .2s, background .2s;
  gap: 6px;
}
.lbtn:hover { border-color: var(--green-mid); background: var(--green-light); }
.lbtn-left { display: flex; align-items: center; gap: 8px; }
.lbtn-left i { font-size: 15px; color: var(--green-mid); flex-shrink: 0; }
.lbtn .fa-chevron-right { font-size: 9px; color: var(--muted); }

/* Stay connected */
.social-row { display: flex; gap: 10px; margin-top: 14px; }
.soc-btn {
  width: 32px; height: 32px; border-radius: 50%;
  display: flex; align-items: center; justify-content: center;
  color: #fff; font-size: 13px; text-decoration: none;
  transition: opacity .2s;
}
.soc-btn:hover { opacity: .85; }
.fb { background: #1877F2; }
.ig { background: linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888); }
.tw { background: #1DA1F2; }
.yt { background: #FF0000; }

/* ===== COPYRIGHT ===== */
.copyright {
  text-align: center; font-size: 12px; color: var(--muted);
  margin-top: 24px;
}
.copyright i { color: #9ca3af; margin-right: 4px; }

/* ===== SIDEBAR LEAF DECORATION ===== */
.sidebar-leaf {
  position: absolute; bottom: 90px; right: -8px;
  width: 60px; opacity: .12;
  pointer-events: none;
}

/* ===================== RESPONSIVE ===================== */

/* Large tablet */
@media (max-width: 1180px) {
  .footer-grid {
    grid-template-columns: 1fr 1fr;
  }
}

/* Tablet */
@media (max-width: 960px) {
  :root { --sidebar-w: 220px; }
  .stats { grid-template-columns: repeat(2,1fr); }
  .tables-grid { grid-template-columns: 1fr; }
  .footer-grid { grid-template-columns: 1fr 1fr; }
}

/* Mobile sidebar collapse */
@media (max-width: 768px) {
  .menu-toggle { display: flex; }

  .sidebar {
    transform: translateX(-100%);
  }
  .sidebar.open { transform: translateX(0); }
  .overlay.open { display: block; }

  .main { margin-left: 0; padding: 64px 16px 40px; }

  .hero { padding: 28px 20px; min-height: 130px; }
  .hero-text h1 { font-size: 20px; }
  .hero-img { height: 100%; opacity: .35; }

  .stats { grid-template-columns: repeat(2,1fr); gap: 12px; }
  .tables-grid { grid-template-columns: 1fr; }

  .footer-grid { grid-template-columns: 1fr; }

  /* Horizontal scroll tables on small phones */
  .panel { overflow-x: auto; }
  .tbl { min-width: 480px; }
}

@media (max-width: 480px) {
  .stats { grid-template-columns: 1fr 1fr; gap: 10px; }
  .stat-num { font-size: 22px; }
  .hero-text h1 { font-size: 18px; }
  .hero-text p { font-size: 12.5px; }
}
</style>
</head>
<body>

<!-- Mobile toggle -->
<button class="menu-toggle" id="menuToggle" aria-label="Open menu">
  <i class="fa-solid fa-bars"></i>
</button>
<div class="overlay" id="overlay"></div>

<!-- SIDEBAR -->
<aside class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="brand-logo"><i class="fa-solid fa-paw"></i></div>
    <div class="brand-text">
      <strong>ARS</strong>
      <span>Animal Rescue System</span>
    </div>
  </div>
  <nav class="sidebar-nav">
    <a href="dashboard.php" class="active"><i class="fa-solid fa-house"></i> Dashboard</a>
    <a href="report.php"><i class="fa-solid fa-flag"></i> Report</a>
    <a href="surrender.php"><i class="fa-solid fa-hand"></i> Surrender</a>
    <a href="events.php"><i class="fa-solid fa-calendar-days"></i> Events</a>
    <a href="resources.php"><i class="fa-solid fa-book-open"></i> Resources</a>
    <a href="about.php"><i class="fa-solid fa-circle-info"></i> About</a>
  </nav>
  <div class="sidebar-footer">
    <div class="paw-icon"><i class="fa-solid fa-shield-dog"></i></div>
    <p>Every rescue makes<br>a difference</p>
  </div>
</aside>

<!-- MAIN -->
<main class="main">

  <!-- HERO -->
  <section class="hero">
    <div class="hero-text">
      <h1>Animal Rescue System</h1>
      <p>Help report missing or found domestic animals,<br>wildlife near homes, and wildlife in critical condition.</p>
    </div>
    <img class="hero-img" src="https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=340&h=280&fit=crop&crop=faces" alt="Dog and cat">
  </section>

  <!-- STATS -->
  <section class="stats">
    <div class="stat-card">
      <div class="stat-icon ic-green"><i class="fa-solid fa-paw"></i></div>
      <div>
        <div class="stat-num"><?= $rescued ?></div>
        <div class="stat-lbl">Animals Helped</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon ic-blue"><i class="fa-solid fa-magnifying-glass"></i></div>
      <div>
        <div class="stat-num"><?= $missingReports ?></div>
        <div class="stat-lbl">Missing Reports</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon ic-teal"><i class="fa-solid fa-location-dot"></i></div>
      <div>
        <div class="stat-num"><?= $foundReports ?></div>
        <div class="stat-lbl">Found Reports</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="stat-icon ic-green"><i class="fa-solid fa-tree"></i></div>
      <div>
        <div class="stat-num"><?= $wildlifeReports ?></div>
        <div class="stat-lbl">Wildlife Reports</div>
      </div>
    </div>
  </section>

  <!-- TABLES -->
  <section class="tables-grid">
    <!-- Missing -->
    <div class="panel">
      <div class="panel-head">
        <h2>Reported Missing Domestic Animals</h2>
        <a href="#">View All</a>
      </div>
      <table class="tbl">
        <thead>
          <tr>
            <th>Animal</th>
            <th>Location</th>
            <th>Status</th>
            <th>Date Reported</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($missingAnimals as $a):
            $d = new DateTime($a['created_at']); ?>
          <tr>
            <td>
              <div class="cell-animal">
                <img src="<?= htmlspecialchars($a['image']) ?>" alt="animal">
                <div>
                  <div class="ci-name"><?= htmlspecialchars($a['animal_name']) ?></div>
                  <div class="ci-sub"><?= htmlspecialchars($a['breed']) ?></div>
                </div>
              </div>
            </td>
            <td>
              <div class="cell-loc">
                <i class="fa-solid fa-location-dot"></i>
                <div>
                  <div class="ci-name"><?= htmlspecialchars($a['location']) ?></div>
                  <div class="ci-sub">Puerto Princesa</div>
                </div>
              </div>
            </td>
            <td><span class="pill pill-pending">Pending</span></td>
            <td>
              <div class="ci-name"><?= $d->format('M d, Y') ?></div>
              <div class="ci-sub"><?= $d->format('h:i A') ?></div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div class="view-all-row"><a href="#">View All</a></div>
    </div>

    <!-- Found -->
    <div class="panel">
      <div class="panel-head">
        <h2>Domestic Animals Found by the Community</h2>
        <a href="#">View All</a>
      </div>
      <table class="tbl">
        <thead>
          <tr>
            <th>Animal</th>
            <th>Location Found</th>
            <th>Status</th>
            <th>Date Found</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($foundAnimals as $a):
            $d = new DateTime($a['created_at']); ?>
          <tr>
            <td>
              <div class="cell-animal">
                <img src="<?= htmlspecialchars($a['image']) ?>" alt="animal">
                <div>
                  <div class="ci-name">Unknown</div>
                  <div class="ci-sub"><?= htmlspecialchars($a['breed']) ?></div>
                </div>
              </div>
            </td>
            <td>
              <div class="cell-loc">
                <i class="fa-solid fa-location-dot"></i>
                <div>
                  <div class="ci-name"><?= htmlspecialchars($a['location']) ?></div>
                  <div class="ci-sub">Puerto Princesa</div>
                </div>
              </div>
            </td>
            <td><span class="pill pill-found">Found</span></td>
            <td>
              <div class="ci-name"><?= $d->format('M d, Y') ?></div>
              <div class="ci-sub"><?= $d->format('h:i A') ?></div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div class="view-all-row"><a href="#">View All</a></div>
    </div>
  </section>

  <!-- FOOTER GRID -->
  <section class="footer-grid">

    <!-- About -->
    <div class="fbox">
      <div class="about-head">
        <div class="about-icon"><i class="fa-solid fa-paw"></i></div>
        <h3 class="fbox-title" style="margin:0">About ARS</h3>
      </div>
      <p>The Animal Rescue System (ARS) helps the community report domestic animals, wildlife found near homes, wildlife in critical condition, and wildlife surrender cases. Together, we can protect and save more lives.</p>
    </div>

    <!-- News & Events -->
    <div class="fbox">
      <div class="news-head">
        <h3>Latest News &amp; Events</h3>
        <a href="#">View All</a>
      </div>
      <?php foreach ($events as $ev): ?>
      <div class="news-item">
        <img src="<?= htmlspecialchars($ev['image']) ?>" alt="event">
        <div>
          <div class="ni-title"><?= htmlspecialchars($ev['title']) ?></div>
          <div class="ni-meta">
            <span><i class="fa-regular fa-calendar" style="margin-right:3px"></i><?= $ev['date'] ?></span>
            <span class="ni-tag <?= $ev['tag']==='Event'?'tag-event':'tag-news' ?>"><?= $ev['tag'] ?></span>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>

    <!-- Quick Links -->
    <div class="fbox">
      <div class="fbox-title">Quick Links</div>
      <div class="links-grid">
        <a href="report.php" class="lbtn">
          <div class="lbtn-left"><i class="fa-solid fa-magnifying-glass"></i><span>Report Missing<br>Domestic Animal</span></div>
          <i class="fa-solid fa-chevron-right"></i>
        </a>
        <a href="report.php" class="lbtn">
          <div class="lbtn-left"><i class="fa-solid fa-location-dot"></i><span>Report Found<br>Domestic Animal</span></div>
          <i class="fa-solid fa-chevron-right"></i>
        </a>
        <a href="report.php" class="lbtn">
          <div class="lbtn-left"><i class="fa-solid fa-tree"></i><span>Report Wildlife<br>Animal</span></div>
          <i class="fa-solid fa-chevron-right"></i>
        </a>
        <a href="surrender.php" class="lbtn">
          <div class="lbtn-left"><i class="fa-solid fa-hand-holding-heart"></i><span>Surrender<br>Wildlife Animal</span></div>
          <i class="fa-solid fa-chevron-right"></i>
        </a>
      </div>
    </div>

    <!-- Stay Connected -->
    <div class="fbox">
      <div class="fbox-title">Stay Connected</div>
      <p>Follow us for domestic and wildlife animal updates.</p>
      <div class="social-row">
        <a href="#" class="soc-btn fb"><i class="fa-brands fa-facebook-f"></i></a>
        <a href="#" class="soc-btn ig"><i class="fa-brands fa-instagram"></i></a>
        <a href="#" class="soc-btn tw"><i class="fa-brands fa-twitter"></i></a>
        <a href="#" class="soc-btn yt"><i class="fa-brands fa-youtube"></i></a>
      </div>
    </div>

  </section>

  <div class="copyright">
    <i class="fa-solid fa-paw"></i> &copy; 2025 Animal Rescue System (ARS). All rights reserved.
  </div>

</main>

<script>
  const toggle  = document.getElementById('menuToggle');
  const sidebar = document.getElementById('sidebar');
  const overlay = document.getElementById('overlay');

  function openMenu()  { sidebar.classList.add('open'); overlay.classList.add('open'); }
  function closeMenu() { sidebar.classList.remove('open'); overlay.classList.remove('open'); }

  toggle.addEventListener('click', openMenu);
  overlay.addEventListener('click', closeMenu);
</script>
</body>
</html>
