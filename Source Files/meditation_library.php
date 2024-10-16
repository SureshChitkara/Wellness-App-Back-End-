<?php
// Include the database connection
include 'db_connect.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meditation Library</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #9c28c2;
            padding: 20px;
            color: white;
            text-align: center;
        }
        .container {
            padding: 20px;
        }
        .meditation-track {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #e0e0e0;
            border-radius: 10px;
        }
        audio {
            width: 100%;
            margin-top: 10px;
        }
    </style>
</head>
<body>

<header>
    <h1>Meditation Library</h1>
    <p>Relax and explore a variety of meditation tracks.</p>
</header>

<div class="container">
    <?php
    // Fetch meditation tracks from the database
    $stmt = $pdo->query('SELECT * FROM meditation_tracks');
    $tracks = $stmt->fetchAll();

    // Check if there are any tracks
    if (count($tracks) > 0) {
        foreach ($tracks as $track) {
            echo "<div class='meditation-track'>";
            echo "<h2>" . htmlspecialchars($track['title']) . "</h2>";
            echo "<p>" . htmlspecialchars($track['description']) . "</p>";
            echo "<p>Duration: " . htmlspecialchars($track['duration']) . "</p>";
            echo "<audio controls><source src='uploads/" . htmlspecialchars($track['audio_file']) . "' type='audio/mpeg'></audio>";
            echo "</div>";
        }
    } else {
        echo "<p>No meditation tracks available at the moment. Please check back later.</p>";
    }
    ?>
</div>

<footer>
    <p>&copy; 2024 Wellness App. All rights reserved.</p>
</footer>

</body>
</html>
CREATE TABLE moods (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,  -- Assuming you have user management in place
    mood_level INT,  -- A number representing the mood (e.g., 1-10)
    mood_description VARCHAR(255),  -- Optional description of the mood
    track_id INT,  -- Track the meditation track influencing mood
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
