<?php
require 'db.php';

try {
    $test = $db->test->insertOne([
        "status" => "connected",
        "time" => new MongoDB\BSON\UTCDateTime()
    ]);

    echo "✅ Connected to MongoDB Atlas!";
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}