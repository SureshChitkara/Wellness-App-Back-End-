<?php
include 'db_connect.php';

// Fetch all therapists
$stmt = $pdo->query('SELECT * FROM therapists');
$therapists = $stmt->fetchAll();

foreach ($therapists as $therapist) {
    echo $therapist['name'] . ' - ' . $therapist['specialty'] . '<br>';
}
?>
