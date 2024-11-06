<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    // User is not logged in, redirect to login page
    header('Location: login.php');
    exit();
}

// Include database connection
include 'db_connect.php';

// Fetch meditation tracks from the database
$stmt = $pdo->query('SELECT * FROM meditation_tracks');
$tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Meditation Library</title>
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
        .track {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

<h1>Meditation Library</h1>

<div class="tracks">
    <?php if ($tracks): ?>
        <?php foreach ($tracks as $track): ?>
            <div class="track">
                <h3><?php echo htmlspecialchars($track['title']); ?></h3>
                <audio controls id="audio-<?php echo $track['id']; ?>" onplay="trackPlayed(<?php echo $track['id']; ?>)">
                    <source src="<?php echo htmlspecialchars($track['file_path']); ?>" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No meditation tracks available.</p>
    <?php endif; ?>
</div>

<!-- Back Button -->
<button onclick="goBack()">Go Back</button>

<script>
function goBack() {
    window.history.back();
}

function trackPlayed(trackId) {
    // Ask the user for their mood rating
    let mood = prompt("On a scale of 1-10, how did this track make you feel?");
    
    if (mood >= 1 && mood <= 10) {
        // Create a FormData object to send the data
        let formData = new FormData();
        formData.append('track_id', trackId);
        formData.append('mood', mood);
        
        // Send the mood data to add_meditation.php
        fetch('add_meditation.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (response.ok) {
                console.log("Mood tracked successfully.");
            } else {
                console.error('Error tracking mood:', response.statusText);
            }
        })
        .catch(error => console.error('Error:', error));
    } else {
        alert("Please enter a valid mood rating between 1 and 10.");
    }
}
</script>

</body>
</html>
