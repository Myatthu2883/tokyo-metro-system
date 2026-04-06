<?php
// ============================================================
// Database Configuration - Tokyo Metro Railway Management System
// ============================================================

$db_host = 'localhost';
$db_user = 'root';        // Default XAMPP user
$db_pass = '';             // Default XAMPP password (empty)
$db_name = 'tokyo_metro_db';

// Create connection
$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

// Check connection
if (!$conn) {
    die("<div style='padding:20px;background:#F62E36;color:#fff;font-family:sans-serif;border-radius:8px;margin:20px;'>
        <h3>⚠ Database Connection Failed</h3>
        <p>" . mysqli_connect_error() . "</p>
        <p>Please make sure XAMPP MySQL is running and the database 'tokyo_metro_db' exists.</p>
    </div>");
}

// Set charset
mysqli_set_charset($conn, "utf8mb4");
?>
