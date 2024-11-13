<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Handle file upload and track addition
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['audio_file'])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $mood = $_POST['mood'];
    $audioFile = $_FILES['audio_file'];

    // Ensure file is an MP3
    if ($audioFile['type'] === 'audio/mpeg') {
        $uploadDir = 'uploads/';
        $filePath = $uploadDir . basename($audioFile['name']);

        // Move uploaded file to uploads folder
        if (move_uploaded_file($audioFile['tmp_name'], $filePath)) {
            // Insert new track into the database
            $stmt = $pdo->prepare('INSERT INTO meditation_tracks (title, description, file_path, mood) VALUES (:title, :description, :file_path, :mood)');
            $stmt->execute([
                'title' => $title,
                'description' => $description,
                'file_path' => $filePath,
                'mood' => $mood
            ]);

            echo "<p>Track uploaded successfully.</p>";
        } else {
            echo "<p>Failed to upload file.</p>";
        }
    } else {
        echo "<p>Only MP3 files are allowed.</p>";
    }
}

// Handle track deletion
if (isset($_GET['delete_id'])) {
    $trackId = $_GET['delete_id'];
    // Get file path for deletion
    $stmt = $pdo->prepare('SELECT file_path FROM meditation_tracks WHERE id = :id');
    $stmt->execute(['id' => $trackId]);
    $track = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($track) {
        // Delete file from the server
        if (file_exists($track['file_path'])) {
            unlink($track['file_path']);
        }
        // Delete track record from database
        $stmt = $pdo->prepare('DELETE FROM meditation_tracks WHERE id = :id');
        $stmt->execute(['id' => $trackId]);
        echo "<p>Track deleted successfully.</p>";
    }
}

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
            padding: 20px;
        }
        .track, .upload-form {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #e0e0e0;
            border-radius: 10px;
        }
        button {
            padding: 10px 20px;
            background-color: #9c28c2;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
    <script>
        function logTrackPlay(trackId, mood) {
            fetch('log_mood.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ track_id: trackId, mood: mood })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    console.log("Track play logged successfully.");
                } else {
                    console.error("Error logging track play:", data.error);
                }
            })
            .catch(error => console.error("Error:", error));
        }
    </script>
</head>
<body>

<h1>Meditation Library</h1>

<!-- Back Button -->
<button onclick="window.history.back()">Go Back</button>

<!-- Upload Form for New Track -->
<div class="upload-form">
    <h2>Upload a New Meditation Track</h2>
    <form method="POST" enctype="multipart/form-data">
        <label for="title">Track Title:</label>
        <input type="text" name="title" id="title" required>

        <label for="description">Description:</label>
        <textarea name="description" id="description" required></textarea>

        <label for="audio_file">Select MP3 File:</label>
        <input type="file" name="audio_file" id="audio_file" accept=".mp3" required>

        <label for="mood">Mood (1-10):</label>
        <input type="number" name="mood" id="mood" min="1" max="10" required>

        <button type="submit">Upload Track</button>
    </form>
</div>

<!-- Display Available Tracks -->
<h2>Available Meditation Tracks</h2>
<div>
    <?php foreach ($tracks as $track): ?>
        <div class="track">
            <h3><?= htmlspecialchars($track['title']) ?></h3>
            <p><?= htmlspecialchars($track['description']) ?></p>
            <p><strong>Mood:</strong> <?= htmlspecialchars($track['mood']) ?></p>
            <audio controls onplay="logTrackPlay(<?= htmlspecialchars($track['id']) ?>, <?= htmlspecialchars($track['mood']) ?>)">
                <source src="<?= htmlspecialchars($track['file_path']) ?>" type="audio/mpeg">
                Your browser does not support the audio element.
            </audio>
            <form method="GET" style="display:inline;">
                <input type="hidden" name="delete_id" value="<?= htmlspecialchars($track['id']) ?>">
                <button type="submit">Delete Track</button>
            </form>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
