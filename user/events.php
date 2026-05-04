<!DOCTYPE html>
<html>
<head>
    <title>Events - ARS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- USE DASHBOARD STYLE -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">

    <!-- ICONS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
</head>
<body>

<div class="layout">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2><i class="fa-solid fa-paw"></i> ARS</h2>

        <a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
        <a href="report.php"><i class="fa-solid fa-flag"></i> Report</a>
        <a href="surrender.php"><i class="fa-solid fa-hand"></i> Surrender</a>
        <a href="events.php" class="active"><i class="fa-solid fa-calendar"></i> Events</a>
        <a href="resources.php"><i class="fa-solid fa-book"></i> Resources</a>
        <a href="about.php"><i class="fa-solid fa-circle-info"></i> About</a>
    </div>

    <!-- MAIN -->
    <div class="main">
        <div class="container">

            <!-- HEADER -->
            <div class="header">
                <h1>📅 Upcoming Events</h1>
            </div>

            <!-- EVENTS GRID (same content, just styled better) -->
            <div class="grid" style="grid-template-columns: repeat(3,1fr); gap:20px;">

                <!-- EVENT 1 -->
                <div class="box">
                    <img src="../assets/images/event1.jpg" style="width:100%; border-radius:10px; margin-bottom:10px;">
                    <h3>Missing Animal Reporting Drive</h3>
                    <p>📍 Puerto Princesa</p>
                    <p>📅 July 15, 2026</p>
                    <p>Join us in helping residents report missing and found domestic animals.</p>
                </div>

                <!-- EVENT 2 -->
                <div class="box">
                    <img src="../assets/images/event2.jpg" style="width:100%; border-radius:10px; margin-bottom:10px;">
                    <h3>Rescue Operation</h3>
                    <p>📍 Narra, Palawan</p>
                    <p>📅 August 2, 2026</p>
                    <p>Help our team rescue animals in danger.</p>
                </div>

                <!-- EVENT 3 -->
                <div class="box">
                    <img src="../assets/images/event3.jpg" style="width:100%; border-radius:10px; margin-bottom:10px;">
                    <h3>Awareness Campaign</h3>
                    <p>📍 Schools & Communities</p>
                    <p>📅 August 20, 2026</p>
                    <p>Learn how to protect and care for animals.</p>
                </div>

            </div>

        </div>
    </div>

</div>

</body>
</html>
