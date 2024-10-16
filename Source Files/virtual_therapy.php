<?php
// Include database connection
include 'db_connect.php';

// Fetch therapists from the database
$stmt = $pdo->query('SELECT * FROM therapists');
$therapists = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Therapy</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
            color: #4a90e2;
        }
        .therapist-list {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-around;
        }
        .therapist-card {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 10px;
            padding: 20px;
            margin: 10px;
            width: 300px;
            text-align: center;
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1);
        }
        .therapist-card img {
            width: 100px;
            height: 100px;
            border-radius: 50%;
        }
        .therapist-card h2 {
            margin: 10px 0;
            font-size: 20px;
        }
        .therapist-card p {
            font-size: 14px;
            color: #666;
        }
        .therapist-card button {
            background-color: #4a90e2;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
        .therapist-card button:hover {
            background-color: #357abd;
        }
    </style>
</head>
<body>

<h1>Meet Our Therapists</h1>

<div class="therapist-list">
    <?php
    foreach ($therapists as $therapist) {
        echo "<div class='therapist-card'>";
        echo "<img src='" . $therapist['profile_picture'] . "' alt='" . $therapist['name'] . "'>";
        echo "<h2>" . $therapist['name'] . "</h2>";
        echo "<p><strong>Specialty:</strong> " . $therapist['specialty'] . "</p>";
        echo "<p>" . $therapist['bio'] . "</p>";
        echo "<p><strong>Availability:</strong> " . $therapist['availability'] . "</p>";
        echo "<button onclick=\"window.location.href='schedule_appointment.php?therapist_id=" . $therapist['id'] . "'\">Schedule Appointment</button>";
        echo "</div>";
    }
    ?>
</div>

</body>
</html>
