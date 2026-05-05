<?php
require '../db.php';

$db->users->insertMany([
    [
        'username' => 'superadmin',
        'password' => password_hash('1234', PASSWORD_DEFAULT),
        'role' => 'super_admin'
    ],
    [
        'username' => 'admin',
        'password' => password_hash('1234', PASSWORD_DEFAULT),
        'role' => 'admin'
    ],
    [
        'username' => 'domestic',
        'password' => password_hash('1234', PASSWORD_DEFAULT),
        'role' => 'domestic_admin'
    ]
]);

echo "Users created!";
