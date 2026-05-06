<?php
require '../db.php';

// ── STAT COUNTS (approved only) ─────────────────────────────────────────────
$domesticMissing  = $db->reports->countDocuments(['status'=>'approved','report_type'=>'missing','animal_category'=>['$ne'=>'wildlife']]);
$domesticFound    = $db->reports->countDocuments(['status'=>'approved','report_type'=>'found',  'animal_category'=>['$ne'=>'wildlife']]);
$wildlifeReports  = $db->reports->countDocuments(['status'=>'approved','animal_category'=>'wildlife']);
$totalDomestic    = $domesticMissing + $domesticFound;
$totalWildlife    = $wildlifeReports;

// ── APPROVED ANIMAL LISTS ────────────────────────────────────────────────────
$missingAnimals = $db->reports->find(
  ['status'=>'approved','animal_category'=>['$ne'=>'wildlife'],'report_type'=>'missing'],
  ['sort'=>['created_at'=>-1],'limit'=>5]
);
$foundAnimals = $db->reports->find(
  ['status'=>'approved','animal_category'=>['$ne'=>'wildlife'],'report_type'=>'found'],
  ['sort'=>['created_at'=>-1],'limit'=>5]
);

// ── APPROVED EVENTS ──────────────────────────────────────────────────────────
$domesticEvents  = $db->events->find(['status'=>'approved','category'=>'domestic'], ['sort'=>['_id'=>-1],'limit'=>2]);
$wildlifeEvents  = $db->events->find(['status'=>'approved','category'=>'wildlife'], ['sort'=>['_id'=>-1],'limit'=>2]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Dashboard – ARS</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
/* ── RESET & VARS ─────────────────────────────────────────────────────────── */
*,*::before,*::after{margin:0;padding:0;box-sizing:border-box}
:root{
  --sw:240px;
  --sbg:#0a3a2a;--shov:#144d38;
  --gm:#1e8e3e;--gl:#e6f4ea;
  --bl:#e8f0fe;--b:#1a73e8;
  --tl:#e0f2f1;--t:#00897b;
  --ol:#fff3e0;--o:#e65100;
  --bg:#f4f6f5;--card:#fff;
  --txt:#1a2e22;--mut:#6b7280;--bdr:#eef0ee;
  --r:12px;--sh:0 1px 4px rgba(0,0,0,.07);
  --font:'DM Sans',sans-serif;
}
body{font-family:var(--font);background:var(--bg);color:var(--txt);display:flex;min-height:100vh}

/* ── SIDEBAR ─────────────────────────────────────────────────────────────── */
.sidebar{width:var(--sw);background:var(--sbg);color:#fff;display:flex;flex-direction:column;padding:20px 14px 24px;position:fixed;top:0;left:0;bottom:0;z-index:200;transition:transform .3s}
.sb-brand{display:flex;align-items:center;gap:10px;padding:4px 8px 24px;border-bottom:1px solid rgba(255,255,255,.08);margin-bottom:14px}
.sb-logo{width:38px;height:38px;background:#fff;border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--sbg);font-size:18px;flex-shrink:0}
.sb-brand strong{font-size:18px;font-weight:700;display:block;line-height:1.1}
.sb-brand span{font-size:10.5px;opacity:.75}
.sb-nav{display:flex;flex-direction:column;gap:4px;flex:1}
.sb-nav a{display:flex;align-items:center;gap:11px;color:rgba(255,255,255,.82);text-decoration:none;padding:10px 14px;border-radius:8px;font-size:14px;font-weight:500;transition:background .18s,color .18s}
.sb-nav a i{width:18px;font-size:15px;text-align:center}
.sb-nav a:hover{background:var(--shov);color:#fff}
.sb-nav a.active{background:rgba(255,255,255,.15);color:#fff;font-weight:600}
.sb-foot{margin-top:24px;background:rgba(0,0,0,.18);border:1px solid rgba(255,255,255,.06);border-radius:10px;padding:18px 16px;text-align:center}
.sb-foot i{font-size:30px;color:#4ade80;opacity:.85;margin-bottom:8px}
.sb-foot p{font-size:12.5px;line-height:1.45;font-weight:500;opacity:.9}

/* ── MOBILE ────────────────────────────────────────────────────────────────── */
.menu-btn{display:none;position:fixed;top:14px;left:14px;z-index:300;background:var(--sbg);color:#fff;border:none;cursor:pointer;width:38px;height:38px;border-radius:8px;font-size:16px;align-items:center;justify-content:center}
.overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.45);z-index:190}

/* ── MAIN ─────────────────────────────────────────────────────────────────── */
.main{margin-left:var(--sw);padding:24px 28px 40px;flex:1;min-width:0}

/* ── HERO ─────────────────────────────────────────────────────────────────── */
.hero{background:linear-gradient(100deg,#0a3a2a 45%,rgba(10,58,42,.55) 75%,transparent 100%),url('https://images.unsplash.com/photo-1450778869180-41d0601e046e?w=1200&h=280&fit=crop') center/cover no-repeat;border-radius:var(--r);padding:36px 40px;color:#fff;position:relative;overflow:hidden;margin-bottom:22px;min-height:148px;display:flex;align-items:center}
.hero h1{font-size:26px;font-weight:700;margin-bottom:8px}
.hero p{font-size:13.5px;opacity:.88;max-width:380px;line-height:1.55}
.hero-img{position:absolute;right:0;bottom:0;height:130%;object-fit:contain;pointer-events:none}

/* ── STATS ────────────────────────────────────────────────────────────────── */
.stats{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:22px}
.stat-card{background:var(--card);border-radius:var(--r);padding:18px 20px;display:flex;align-items:center;gap:14px;box-shadow:var(--sh)}
.s-icon{width:46px;height:46px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:19px;flex-shrink:0}
.ic-g{background:var(--gl);color:var(--gm)}
.ic-b{background:var(--bl);color:var(--b)}
.ic-t{background:var(--tl);color:var(--t)}
.s-num{font-size:26px;font-weight:700;line-height:1}
.s-lbl{font-size:12px;color:var(--mut);font-weight:500;margin-top:2px;line-height:1.3}

/* ── PANELS / TABLES ────────────────────────────────────────────────────────── */
.tables-grid{display:grid;grid-template-columns:1fr 1fr;gap:20px;margin-bottom:22px}
.panel{background:var(--card);border-radius:var(--r);padding:20px 22px;box-shadow:var(--sh);overflow:hidden}
.ph{display:flex;justify-content:space-between;align-items:center;margin-bottom:14px}
.ph h2{font-size:14px;font-weight:600}
.ph a{font-size:12.5px;color:var(--b);text-decoration:none;font-weight:500}
.tbl{width:100%;border-collapse:collapse}
.tbl th{text-align:left;font-size:11.5px;color:var(--mut);font-weight:500;padding-bottom:10px;border-bottom:1px solid var(--bdr)}
.tbl td{padding:10px 0;border-bottom:1px solid var(--bdr);vertical-align:middle;font-size:13px}
.tbl tr:last-child td{border-bottom:none}
.ca{display:flex;align-items:center;gap:10px}
.ca img{width:34px;height:34px;border-radius:7px;object-fit:cover}
.cn{font-size:13px;font-weight:600;line-height:1.2}
.cs{font-size:11.5px;color:var(--mut)}
.cl{display:flex;align-items:flex-start;gap:6px}
.cl i{color:var(--mut);font-size:12px;margin-top:2px}
.pill{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.pill-p{background:var(--ol);color:var(--o)}
.pill-f{background:var(--gl);color:var(--gm)}
.va-row{text-align:center;margin-top:12px}
.va-row a{font-size:13px;color:var(--b);text-decoration:none;font-weight:500}

/* empty state */
.empty-state{text-align:center;padding:32px 16px;color:var(--mut);font-size:13px}
.empty-state i{font-size:32px;margin-bottom:8px;display:block;opacity:.4}

/* ── FOOTER GRID ────────────────────────────────────────────────────────────── */
.footer-grid{display:grid;grid-template-columns:1fr 1fr 1.5fr 1fr;gap:18px}
.fbox{background:var(--card);border-radius:var(--r);padding:20px;box-shadow:var(--sh)}
.ft{font-size:13.5px;font-weight:600;margin-bottom:12px}

/* About */
.ab-head{display:flex;align-items:center;gap:10px;margin-bottom:10px}
.ab-icon{width:36px;height:36px;background:var(--gl);color:var(--gm);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:16px;flex-shrink:0}
.fbox p{font-size:12.5px;color:var(--mut);line-height:1.6}

/* News */
.nh{display:flex;justify-content:space-between;align-items:center;margin-bottom:10px}
.nh h3{font-size:13.5px;font-weight:600}
.nh a{font-size:12px;color:var(--b);text-decoration:none}
.news-section-label{font-size:11px;font-weight:600;color:var(--mut);text-transform:uppercase;letter-spacing:.04em;margin:10px 0 8px;padding-top:10px;border-top:1px solid var(--bdr)}
.news-section-label:first-of-type{border-top:none;margin-top:0;padding-top:0}
.ni{display:flex;gap:10px;margin-bottom:12px}
.ni:last-child{margin-bottom:0}
.ni img{width:44px;height:44px;border-radius:8px;object-fit:cover;flex-shrink:0}
.ni-t{font-size:12px;font-weight:600;line-height:1.35;margin-bottom:4px}
.ni-m{display:flex;justify-content:space-between;align-items:center;font-size:10.5px;color:var(--mut)}
.tag{padding:2px 7px;border-radius:4px;font-size:10px;font-weight:600}
.tag-e{background:var(--gl);color:var(--gm)}
.tag-n{background:var(--bl);color:var(--b)}

/* Quick links */
.lg{display:grid;grid-template-columns:1fr 1fr;gap:10px}
.lb{display:flex;align-items:center;justify-content:space-between;padding:10px 11px;border:1.5px solid var(--bdr);border-radius:9px;text-decoration:none;color:var(--txt);font-size:11.5px;font-weight:500;transition:border-color .2s,background .2s;gap:6px}
.lb:hover{border-color:var(--gm);background:var(--gl)}
.lb-l{display:flex;align-items:center;gap:8px}
.lb-l i{font-size:15px;color:var(--gm);flex-shrink:0}
.lb .fa-chevron-right{font-size:9px;color:var(--mut)}

/* Stay connected – two-column split */
.sc-split{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.sc-col h4{font-size:11.5px;font-weight:600;color:var(--gm);margin-bottom:8px;display:flex;align-items:center;gap:5px}
.sc-col h4 i{font-size:12px}
.soc{display:flex;gap:8px;flex-wrap:wrap}
.sb2{width:30px;height:30px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:12px;text-decoration:none;transition:opacity .2s}
.sb2:hover{opacity:.82}
.fb{background:#1877F2}.ig{background:linear-gradient(45deg,#f09433,#e6683c,#dc2743,#cc2366,#bc1888)}.tw{background:#1DA1F2}.yt{background:#FF0000}.tk{background:#000000}

/* copyright */
.copy{text-align:center;font-size:12px;color:var(--mut);margin-top:24px}
.copy i{color:#9ca3af;margin-right:4px}

/* ── RESPONSIVE ─────────────────────────────────────────────────────────────── */
@media(max-width:1200px){.footer-grid{grid-template-columns:1fr 1fr}}
@media(max-width:960px){:root{--sw:220px}.stats{grid-template-columns:repeat(2,1fr)}.tables-grid{grid-template-columns:1fr}.footer-grid{grid-template-columns:1fr 1fr}}
@media(max-width:768px){
  .menu-btn{display:flex}
  .sidebar{transform:translateX(-100%)}
  .sidebar.open{transform:translateX(0)}
  .overlay.open{display:block}
  .main{margin-left:0;padding:64px 16px 40px}
  .hero{padding:28px 20px}.hero h1{font-size:20px}.hero-img{height:100%;opacity:.3}
  .stats{grid-template-columns:repeat(2,1fr);gap:12px}
  .tables-grid{grid-template-columns:1fr}
  .footer-grid{grid-template-columns:1fr}
  .panel{overflow-x:auto}.tbl{min-width:480px}
}
@media(max-width:480px){.stats{grid-template-columns:1fr 1fr;gap:10px}.s-num{font-size:22px}.hero h1{font-size:18px}}
</style>
</head>
<body>

<button class="menu-btn" id="menuBtn" aria-label="Menu"><i class="fa-solid fa-bars"></i></button>
<div class="overlay" id="overlay"></div>

<aside class="sidebar" id="sidebar">
  <div class="sb-brand">
    <div class="sb-logo"><i class="fa-solid fa-paw"></i></div>
    <div>
      <strong>ARS</strong>
      <span>Animal Rescue System</span>
    </div>
  </div>
  <nav class="sb-nav">
    <a href="dashboard.php" class="active"><i class="fa-solid fa-house"></i> Dashboard</a>
    <a href="report.php"><i class="fa-solid fa-flag"></i> Report</a>
    <a href="surrender.php"><i class="fa-solid fa-hand"></i> Surrender</a>
    <a href="events.php"><i class="fa-solid fa-calendar-days"></i> Events</a>
    <a href="resources.php"><i class="fa-solid fa-book-open"></i> Resources</a>
    <a href="about.php"><i class="fa-solid fa-circle-info"></i> About</a>
  </nav>
  <div class="sb-foot">
    <i class="fa-solid fa-shield-dog"></i>
    <p>Every rescue makes<br>a difference</p>
  </div>
</aside>

<main class="main">

  <section class="hero">
    <div>
      <h1>Animal Rescue System</h1>
      <p>Help report missing or found domestic animals, wildlife near homes, and wildlife in critical condition.</p>
    </div>
    <img class="hero-img" src="https://images.unsplash.com/photo-1587300003388-59208cc962cb?w=340&h=280&fit=crop&crop=faces" alt="Animals">
  </section>

  <section class="stats">
    <div class="stat-card">
      <div class="s-icon ic-g"><i class="fa-solid fa-house-chimney-medical"></i></div>
      <div>
        <div class="s-num"><?= $totalDomestic ?></div>
        <div class="s-lbl">Total Domestic Animals Reunited with Owner</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="s-icon ic-b"><i class="fa-solid fa-magnifying-glass"></i></div>
      <div>
        <div class="s-num"><?= $domesticMissing ?></div>
        <div class="s-lbl">Total Domestic Animals Missing</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="s-icon ic-t"><i class="fa-solid fa-location-dot"></i></div>
      <div>
        <div class="s-num"><?= $domesticFound ?></div>
        <div class="s-lbl">Total Domestic Animals Found by Community</div>
      </div>
    </div>
    <div class="stat-card">
      <div class="s-icon ic-g"><i class="fa-solid fa-tree"></i></div>
      <div>
        <div class="s-num"><?= $totalWildlife ?></div>
        <div class="s-lbl">Total Wildlife Reports</div>
      </div>
    </div>
  </section>

  <section class="tables-grid">

    <div class="panel">
      <div class="ph">
        <h2>Reported Missing Domestic Animals</h2>
        <a href="report.php?type=missing">View All</a>
      </div>
      <?php $missingList = iterator_to_array($missingAnimals); ?>
      <?php if (empty($missingList)): ?>
        <div class="empty-state"><i class="fa-solid fa-paw"></i>No missing animals reported yet.</div>
      <?php else: ?>
      <table class="tbl">
        <thead><tr><th>Animal</th><th>Location</th><th>Status</th><th>Date Reported</th></tr></thead>
        <tbody>
          <?php foreach ($missingList as $a):
            $img = htmlspecialchars($a['image'] ?? '../assets/images/bg.jpg');
            $name = htmlspecialchars($a['animal_name'] ?? 'Unknown');
            $breed = htmlspecialchars($a['breed'] ?? 'Domestic Animal');
            $loc = htmlspecialchars($a['location'] ?? 'N/A');
            $d = new DateTime($a['created_at'] ?? 'now'); ?>
          <tr>
            <td><div class="ca"><img src="<?=$img?>" alt="animal"><div><div class="cn"><?=$name?></div><div class="cs"><?=$breed?></div></div></div></td>
            <td><div class="cl"><i class="fa-solid fa-location-dot"></i><div><div class="cn"><?=$loc?></div><div class="cs">Puerto Princesa</div></div></div></td>
            <td><span class="pill pill-p">Missing</span></td>
            <td><div class="cn"><?=$d->format('M d, Y')?></div><div class="cs"><?=$d->format('h:i A')?></div></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div class="va-row"><a href="report.php?type=missing">View All</a></div>
      <?php endif; ?>
    </div>

    <div class="panel">
      <div class="ph">
        <h2>Domestic Animals Found by the Community</h2>
        <a href="report.php?type=found">View All</a>
      </div>
      <?php $foundList = iterator_to_array($foundAnimals); ?>
      <?php if (empty($foundList)): ?>
        <div class="empty-state"><i class="fa-solid fa-paw"></i>No found animals reported yet.</div>
      <?php else: ?>
      <table class="tbl">
        <thead><tr><th>Animal</th><th>Location Found</th><th>Status</th><th>Date Found</th></tr></thead>
        <tbody>
          <?php foreach ($foundList as $a):
            $img = htmlspecialchars($a['image'] ?? '../assets/images/bg.jpg');
            $breed = htmlspecialchars($a['breed'] ?? 'Mixed Breed');
            $loc = htmlspecialchars($a['location'] ?? 'N/A');
            $d = new DateTime($a['created_at'] ?? 'now'); ?>
          <tr>
            <td><div class="ca"><img src="<?=$img?>" alt="animal"><div><div class="cn">Unknown</div><div class="cs"><?=$breed?></div></div></div></td>
            <td><div class="cl"><i class="fa-solid fa-location-dot"></i><div><div class="cn"><?=$loc?></div><div class="cs">Puerto Princesa</div></div></div></td>
            <td><span class="pill pill-f">Found</span></td>
            <td><div class="cn"><?=$d->format('M d, Y')?></div><div class="cs"><?=$d->format('h:i A')?></div></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
      <div class="va-row"><a href="report.php?type=found">View All</a></div>
      <?php endif; ?>
    </div>

  </section>

  <section class="footer-grid">

    <div class="fbox">
      <div class="ab-head">
        <div class="ab-icon"><i class="fa-solid fa-paw"></i></div>
        <div class="ft" style="margin:0">About ARS</div>
      </div>
      <p>The Animal Rescue System (ARS) helps the community report domestic animals, wildlife found near homes, wildlife in critical condition, and wildlife surrender cases. Together, we can protect and save more lives.</p>
    </div>

    <div class="fbox">
      <div class="nh"><h3>Latest News &amp; Events</h3><a href="events.php">View All</a></div>

      <div class="news-section-label"><i class="fa-solid fa-house-chimney-medical" style="margin-right:4px"></i>Domestic</div>
      <?php
        $dEvList = iterator_to_array($domesticEvents);
        if (empty($dEvList)): ?>
        <p style="font-size:12px;color:var(--mut);margin-bottom:6px">No domestic events yet.</p>
      <?php else:
        foreach ($dEvList as $ev):
          $tag = $ev['tag'] ?? 'Event';
          $tagClass = $tag==='Event'?'tag-e':'tag-n'; ?>
        <div class="ni">
          <img src="<?=htmlspecialchars($ev['image']??'../assets/images/bg.jpg')?>" alt="event">
          <div>
            <div class="ni-t"><?=htmlspecialchars($ev['title']??'Event')?></div>
            <div class="ni-m">
              <span><i class="fa-regular fa-calendar" style="margin-right:3px"></i><?=htmlspecialchars($ev['date']??'')?></span>
              <span class="tag <?=$tagClass?>"><?=$tag?></span>
            </div>
          </div>
        </div>
      <?php endforeach; endif; ?>

      <div class="news-section-label"><i class="fa-solid fa-tree" style="margin-right:4px"></i>Wildlife</div>
      <?php
        $wEvList = iterator_to_array($wildlifeEvents);
        if (empty($wEvList)): ?>
        <p style="font-size:12px;color:var(--mut)">No wildlife events yet.</p>
      <?php else:
        foreach ($wEvList as $ev):
          $tag = $ev['tag'] ?? 'Event';
          $tagClass = $tag==='Event'?'tag-e':'tag-n'; ?>
        <div class="ni">
          <img src="<?=htmlspecialchars($ev['image']??'../assets/images/bg.jpg')?>" alt="event">
          <div>
            <div class="ni-t"><?=htmlspecialchars($ev['title']??'Event')?></div>
            <div class="ni-m">
              <span><i class="fa-regular fa-calendar" style="margin-right:3px"></i><?=htmlspecialchars($ev['date']??'')?></span>
              <span class="tag <?=$tagClass?>"><?=$tag?></span>
            </div>
          </div>
        </div>
      <?php endforeach; endif; ?>
    </div>

    <div class="fbox">
      <div class="ft">Quick Links</div>
      <div class="lg">
        <a href="report.php?type=missing" class="lb"><div class="lb-l"><i class="fa-solid fa-magnifying-glass"></i><span>Report Missing<br>Domestic Animal</span></div><i class="fa-solid fa-chevron-right"></i></a>
        <a href="report.php?type=found"   class="lb"><div class="lb-l"><i class="fa-solid fa-location-dot"></i><span>Report Found<br>Domestic Animal</span></div><i class="fa-solid fa-chevron-right"></i></a>
        <a href="report.php?type=wildlife" class="lb"><div class="lb-l"><i class="fa-solid fa-tree"></i><span>Report Wildlife<br>Animal</span></div><i class="fa-solid fa-chevron-right"></i></a>
        <a href="surrender.php" class="lb"><div class="lb-l"><i class="fa-solid fa-hand-holding-heart"></i><span>Surrender<br>Wildlife Animal</span></div><i class="fa-solid fa-chevron-right"></i></a>
      </div>
    </div>

    <div class="fbox">
      <div class="ft">Stay Connected</div>
      <div class="sc-split">
        <div class="sc-col">
          <h4><i class="fa-solid fa-house-chimney-medical"></i> Domestic</h4>
          <div class="soc">
            <a href="#" class="sb2 fb" title="Facebook – Domestic"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#" class="sb2 ig" title="Instagram – Domestic"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" class="sb2 tk" title="TikTok – Domestic"><i class="fa-brands fa-tiktok"></i></a>
          </div>
        </div>
        <div class="sc-col">
          <h4><i class="fa-solid fa-tree"></i> Wildlife</h4>
          <div class="soc">
            <a href="#" class="sb2 fb" title="Facebook – Wildlife"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#" class="sb2 ig" title="Instagram – Wildlife"><i class="fa-brands fa-instagram"></i></a>
            <a href="#" class="sb2 tk" title="TikTok – Wildlife"><i class="fa-brands fa-tiktok"></i></a>
          </div>
        </div>
      </div>
    </div>

  </section>

  <div class="copy">
    <i class="fa-solid fa-paw"></i>&copy; 2025 Animal Rescue System (ARS). All rights reserved.
  </div>

</main>

<script>
const menuBtn = document.getElementById('menuBtn');
const sidebar = document.getElementById('sidebar');
const overlay = document.getElementById('overlay');
menuBtn.addEventListener('click',()=>{ sidebar.classList.add('open'); overlay.classList.add('open'); });
overlay.addEventListener('click',()=>{ sidebar.classList.remove('open'); overlay.classList.remove('open'); });
</script>
</body>
</html>
