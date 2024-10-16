<?php
// Include database connection
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capture form data
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Prepare SQL query
    $sql = "INSERT INTO leads (name, email) VALUES (:name, :email)";
    $stmt = $pdo->prepare($sql);

    // Bind parameters and execute the query
    $stmt->execute(['name' => $name, 'email' => $email]);

    echo "Lead added successfully!";
}
?>
