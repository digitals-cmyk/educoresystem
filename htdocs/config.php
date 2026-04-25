<?php
// config.php

// ==========================================
// INFINITYFREE DATABASE CONFIGURATION
// ==========================================
// Please update these 4 lines with the database credentials from your InfinityFree Client Area / Control Panel.

// 1. Database Host (e.g., sql302.infinityfree.com or sql123.epizy.com)
define('DB_SERVER', 'localhost'); // Change 'localhost' to your actual MySQL hostname

// 2. Database Username (e.g., if0_41508485)
define('DB_USERNAME', 'root'); // Replace 'root' with your InfinityFree username (starts with if0_)

// 3. Database Password
define('DB_PASSWORD', ''); // Replace '' with your InfinityFree vPanel/MySQL password

// 4. Database Name (e.g., if0_41508485_educore)
define('DB_NAME', 'educore_db'); // Replace 'educore_db' with your actual InfinityFree Database Name

try {
    $pdo = new PDO("mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME, DB_USERNAME, DB_PASSWORD);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("ERROR: Could not connect to database. Please check your config.php credentials. Details: " . $e->getMessage());
}

// Ensure session is started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
