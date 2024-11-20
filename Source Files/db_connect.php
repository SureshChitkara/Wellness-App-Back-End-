<?php
// Database connection
$host = 'localhost';
$db = 'wellness_app';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_PERSISTENT => true, // Use persistent connections to reduce overhead
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    // Display a user-friendly message and log error details
    echo "A connection error occurred. Please try again later.";
    error_log("Database Connection Error: " . $e->getMessage(), 0); // Log the error
    exit();
}
?>
