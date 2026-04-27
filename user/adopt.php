<?php
require '../db.php';

/* 🔥 ONLY SHOW ADMIN-APPROVED + ADOPTABLE */
$reports = $db->reports->find([
    'status' => 'approved',
    'adoptable' => true
]);

$surrenders = $db->surrenders->find([
    'status' => 'approved',
    'adoptable' => true
]);

$message = "";

// HANDLE ADOPTION REQUEST
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $name = $_POST['name'];
    $contact = $_POST['contact'];
    $animal = $_POST['animal'];
    $source = $_POST['source'];

    $db->adoptions->insertOne([
        'name' => $name,
        'contact' => $contact,
        'animal' => $animal,
        'source' => $source,
        'status' => 'pending',
        'created_at' => new MongoDB\BSON\UTCDateTime()
    ]);

    $message = "Adoption request submitted!";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Adopt Animal</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../assets/css/dashboard.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <style>
        .menu-btn {
            display: none;
            font-size: 22px;
            background: none;
            border: none;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        .card {
            display: flex;
            gap: 20px;
            background: white;
            border-radius: 15px;
            padding: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            align-items: center;
        }

        .card img {
            width: 180px;
            height: 140px;
            object-fit: cover;
            border-radius: 12px;
        }

        .info {
            flex: 1;
        }

        .info h4 {
            margin: 0;
        }

        .info p {
            margin: 5px 0;
            color: #666;
        }

        .adopt-form {
            display: flex;
            flex-direction: column;
            gap: 8px;
            width: 200px;
        }

        .adopt-form input {
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ddd;
        }

        .adopt-form input:focus {
            border-color: #2d6a4f;
            outline: none;
        }

        .adopt-form button {
            padding: 10px;
            background: linear-gradient(135deg, #2d6a4f, #1b4332);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
        }

        .success {
            background: #d8f3dc;
            padding: 10px;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        /* 🔥 EMPTY STATE */
        .empty-state {
            text-align: center;
            padding: 40px 20px;
            background: white;
            border-radius: 12px;
            color: #666;
            font-size: 15px;
            grid-column: 1 / -1;
        }

        @media (max-width: 768px) {
            .menu-btn { display: block; }

            .card {
                flex-direction: column;
                align-items: flex-start;
            }

            .card img {
                width: 100%;
                height: 180px;
            }

            .adopt-form {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="layout">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2><i class="fa-solid fa-paw"></i> ARS</h2>

        <a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
        <a href="report.php"><i class="fa-solid fa-flag"></i> Report</a>
        <a href="surrender.php"><i class="fa-solid fa-hand"></i> Surrender</a>
        <a href="adopt.php" class="active"><i class="fa-solid fa-heart"></i> Adopt</a>
        <a href="events.php"><i class="fa-solid fa-calendar"></i> Events</a>
        <a href="resources.php"><i class="fa-solid fa-book"></i> Resources</a>
        <a href="about.php"><i class="fa-solid fa-circle-info"></i> About</a>
    </div>

    <!-- MAIN -->
    <div class="main">
        <div class="container">

            <div class="header">
                <button onclick="toggleSidebar()" class="menu-btn">☰</button>
                <h1>Adopt an Animal</h1>
            </div>

            <?php if ($message): ?>
                <div class="success"><?= $message ?></div>
            <?php endif; ?>

            <!-- REPORTS -->
            <h3>From Reported Animals</h3>

            <div class="grid">
                <?php 
                $hasReports = false;
                foreach ($reports as $r): 
                    $hasReports = true;
                ?>
                    <div class="card">
                        <img src="<?= $r['image'] ?>">

                        <div class="info">
                            <h4><?= $r['animal'] ?></h4>
                            <p><?= $r['location'] ?></p>
                        </div>

                        <form method="POST" class="adopt-form">
                            <input type="hidden" name="animal" value="<?= $r['animal'] ?>">
                            <input type="hidden" name="source" value="report">

                            <input type="text" name="name" placeholder="Your Name" required>
                            <input type="text" name="contact" placeholder="Contact Number" required>

                            <button type="submit">❤️ Adopt</button>
                        </form>
                    </div>
                <?php endforeach; ?>

                <?php if (!$hasReports): ?>
                    <div class="empty-state">
                        🐾 No rescued animals ready for adoption now.<br>
                        Please check again later.
                    </div>
                <?php endif; ?>
            </div>

            <!-- SURRENDERS -->
            <h3>From Surrendered Animals</h3>

            <div class="grid">
                <?php 
                $hasSurrenders = false;
                foreach ($surrenders as $s): 
                    $hasSurrenders = true;
                ?>
                    <div class="card">
                        <img src="<?= $s['image'] ?>">

                        <div class="info">
                            <h4><?= $s['animal'] ?></h4>
                        </div>

                        <form method="POST" class="adopt-form">
                            <input type="hidden" name="animal" value="<?= $s['animal'] ?>">
                            <input type="hidden" name="source" value="surrender">

                            <input type="text" name="name" placeholder="Your Name" required>
                            <input type="text" name="contact" placeholder="Contact Number" required>

                            <button type="submit">❤️ Adopt</button>
                        </form>
                    </div>
                <?php endforeach; ?>

                <?php if (!$hasSurrenders): ?>
                    <div class="empty-state">
                        🐶 No surrendered animals ready for adoption now.<br>
                        Please check again later.
                    </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

</div>

<script>
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('show');
}
</script>

</body>
</html>