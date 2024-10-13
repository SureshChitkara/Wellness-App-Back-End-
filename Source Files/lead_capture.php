<?php
include 'db_connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Insert into the database
    $stmt = $pdo->prepare('INSERT INTO leads (name, email) VALUES (?, ?)');
    $stmt->execute([$name, $email]);

    echo "Lead captured successfully!";
}
?>

<form method="POST" action="lead_capture.php">
    <input type="text" name="name" placeholder="Your Name" required>
    <input type="email" name="email" placeholder="Your Email" required>
    <button type="submit">Submit</button>
</form>
