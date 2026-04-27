<?php

require __DIR__ . '/vendor/autoload.php';

$uri = getenv("MONGO_URI");

if (!$uri) {
    die("❌ MONGO_URI not set");
}

$client = new MongoDB\Client($uri);

$db = $client->ars_db;

?>
