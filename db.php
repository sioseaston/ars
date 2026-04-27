<?php

require __DIR__ . '/vendor/autoload.php';

$client = new MongoDB\Client("mongodb+srv://entethegreat:abcd1234@ars-db.0x1ykko.mongodb.net");

$db = $client->ars_db;

?>