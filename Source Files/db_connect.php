<?php
$host = 'localhost';
$db = 'wellness_app';  // Ensure this name matches your actual database name
$user = 'root';
$pass = ''; // Your MySQL root password
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit; // Terminate script if connection fails
}
?>
