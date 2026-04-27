<?php
require '../db.php';

// GET APPROVED REPORTS WITH LOCATION
$reports = $db->reports->find([
    'status' => 'approved',
    'latitude' => ['$exists' => true],
    'longitude' => ['$exists' => true]
]);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Animal Map</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- LEAFLET -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <style>
        body { margin:0; font-family:Arial; }
        #map { height: 100vh; }
    </style>
</head>
<body>

<div id="map"></div>

<script>
// INITIAL MAP (Palawan)
var map = L.map('map').setView([9.7392, 118.7353], 10);

// TILE
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
    attribution: 'OpenStreetMap'
}).addTo(map);

// ADD MARKERS FROM PHP
<?php foreach ($reports as $r): ?>
    L.marker([<?= $r['latitude'] ?>, <?= $r['longitude'] ?>])
        .addTo(map)
        .bindPopup(`
            <b><?= $r['animal'] ?></b><br>
            <?= $r['location'] ?><br>
            <img src="<?= $r['image'] ?>" width="120">
        `);
<?php endforeach; ?>

</script>

</body>
</html>