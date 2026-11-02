<?php
// Database connection for SHAA website
// Put this folder inside: C:\xampp\htdocs\shaa_website
// Import shaa_db.sql into phpMyAdmin first.

$host = 'localhost';
$dbname = 'shaa database application';
$username = 'root';
$password = ''; // default XAMPP password is empty

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
