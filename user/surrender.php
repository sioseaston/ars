<?php
require '../db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $owner = trim($_POST['owner']);
    $contact = trim($_POST['contact']);
    $animal = trim($_POST['animal']);
    $reason = trim($_POST['reason']);
    $location = trim($_POST['location']);
    $animalCategory = 'wildlife';

    $lat = $_POST['latitude'] ?? '';
    $lng = $_POST['longitude'] ?? '';

    if (empty($owner) || empty($contact) || empty($location)) {
        $message = "Name, contact, and location are required!";
    } elseif (!preg_match('/^09\d{9}$/', $contact)) {
        $message = "Phone number must start with 09 and be 11 digits.";
    } elseif (empty($lat) || empty($lng)) {
        $message = "Please select a location on the map!";
    } else {

        $lat = floatval($lat);
        $lng = floatval($lng);

        $imageName = $_FILES['image']['name'];
        $tempName = $_FILES['image']['tmp_name'];

        $uploadPath = "../uploads/" . time() . "_" . $imageName;
        move_uploaded_file($tempName, $uploadPath);

        $db->surrenders->insertOne([
            'owner' => $owner,
            'contact' => $contact,
            'animal' => $animal,
            'animal_category' => $animalCategory,
            'surrender_type' => $animalCategory . '_surrender',
            'reason' => $reason,
            'location' => $location,
            'image' => $uploadPath,
            'latitude' => $lat,
            'longitude' => $lng,
            'status' => 'pending',
            'created_at' => new MongoDB\BSON\UTCDateTime()
        ]);

        $message = "Surrender request submitted successfully!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Surrender Wildlife Animal - ARS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- MAIN CSS -->
    <link rel="stylesheet" href="../assets/css/dashboard.css">

    <!-- ICONS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">

    <!-- MAP -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <style>
        .form-box {
            max-width: 650px;
            margin: 20px auto;
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }

        .form-group {
            display: flex;
            flex-direction: column;
            margin-bottom: 15px;
        }

        .form-group label {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .form-group input,
        .form-group textarea {
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ddd;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: #2d6a4f;
            outline: none;
            box-shadow: 0 0 0 2px rgba(45,106,79,0.1);
        }

        textarea {
            height: 110px;
            resize: none;
        }

        #map {
            height: 260px;
            border-radius: 12px;
            margin-bottom: 15px;
            border: 2px solid #e6f4ea;
        }

        .submit-btn {
            padding: 14px;
            background: linear-gradient(135deg, #2d6a4f, #1b4332);
            color: white;
            border: none;
            border-radius: 10px;
        }

        .menu-btn {
            display: none;
            font-size: 22px;
            background: none;
            border: none;
        }

        .success {
            background: #d8f3dc;
            padding: 10px;
            margin-bottom: 10px;
            text-align: center;
            border-radius: 6px;
        }

        .error {
            background: #ffccd5;
            padding: 10px;
            margin-bottom: 10px;
            text-align: center;
            border-radius: 6px;
        }

        @media (max-width: 768px) {
            .menu-btn { display: block; }
            .form-box { margin: 10px; padding: 20px; }
            #map { height: 200px; }
        }
    </style>
</head>

<body>

<div class="layout">

    <div class="sidebar">
        <h2><i class="fa-solid fa-paw"></i> ARS</h2>

        <a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a>
        <a href="report.php"><i class="fa-solid fa-flag"></i> Report</a>
        <a href="surrender.php" class="active"><i class="fa-solid fa-hand"></i> Surrender</a>
        <a href="events.php"><i class="fa-solid fa-calendar"></i> Events</a>
        <a href="resources.php"><i class="fa-solid fa-book"></i> Resources</a>
        <a href="about.php"><i class="fa-solid fa-circle-info"></i> About</a>
    </div>

    <div class="main">
        <div class="container">

            <div class="header">
                <button onclick="toggleSidebar()" class="menu-btn">☰</button>
                <h1>Surrender a Wildlife Animal</h1>
            </div>

            <div class="form-box">

                <?php if ($message): ?>
                    <div class="<?= strpos($message, 'successfully') !== false ? 'success' : 'error' ?>">
                        <?= $message ?>
                    </div>
                <?php endif; ?>

                <p><strong>Click on the map to select the wildlife location</strong></p>
                <p>Use this form only for wildlife currently in your care that you cannot feed, house, or safely keep.</p>

                <div id="map"></div>

                <form method="POST" enctype="multipart/form-data" onsubmit="return checkMap();">

                    <div class="form-group">
                        <label>Citizen / Owner Name</label>
                        <input type="text" name="owner" required>
                    </div>

                    <div class="form-group">
                        <label>Contact Number</label>
                        <input type="text" name="contact" required>
                    </div>

                    <div class="form-group">
                        <label>Wildlife Type</label>
                        <input type="text" name="animal" placeholder="Example: bird, monkey, snake" required>
                    </div>

                    <div class="form-group">
                        <label>Location</label>
                        <input type="text" name="location" required>
                    </div>

                    <div class="form-group">
                        <label>Reason / Care Details</label>
                        <textarea name="reason" placeholder="For wildlife, explain if it is in your care, cannot be fed, unsafe to keep, or needs turnover." required></textarea>
                    </div>

                    <div class="form-group">
                        <label>Upload Image</label>
                        <input type="file" name="image" accept="image/*" capture="environment" required>
                    </div>

                    <input type="hidden" name="latitude" id="lat">
                    <input type="hidden" name="longitude" id="lng">

                    <button type="submit" class="submit-btn">
                        <i class="fa-solid fa-paper-plane"></i> Submit Wildlife Surrender
                    </button>

                </form>

            </div>

        </div>
    </div>

</div>

<script>
function toggleSidebar() {
    document.querySelector('.sidebar').classList.toggle('show');
}

var map = L.map('map').setView([9.7392, 118.7353], 12);

L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

var marker;

map.on('click', function(e) {
    if (marker) map.removeLayer(marker);
    marker = L.marker(e.latlng).addTo(map);
    document.getElementById("lat").value = e.latlng.lat;
    document.getElementById("lng").value = e.latlng.lng;
});

function checkMap() {
    var lat = document.getElementById("lat").value;
    var lng = document.getElementById("lng").value;

    if (!lat || !lng) {
        alert("Please select a location on the map first!");
        return false;
    }
    return true;
}
</script>

</body>
</html>
