<?php 
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit();
}

// Ensure the uploads directory exists
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

// Handle file upload and track addition
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['audio_file'])) {
    $title = htmlspecialchars(trim($_POST['title']));
    $description = htmlspecialchars(trim($_POST['description']));
    $mood = (int)$_POST['mood'];
    $audioFile = $_FILES['audio_file'];

    if ($audioFile['type'] === 'audio/mpeg' && $mood >= 1 && $mood <= 10) {
        $fileName = time() . '_' . basename($audioFile['name']); // Add timestamp to avoid overwrites
        $filePath = $uploadDir . $fileName;

        if (move_uploaded_file($audioFile['tmp_name'], $filePath)) {
            try {
                // Insert new track into the database (no involvement with meditation_tracking table)
                $stmt = $pdo->prepare('INSERT INTO meditation_tracks (title, description, file_path, mood) VALUES (:title, :description, :file_path, :mood)');
                $stmt->execute([
                    'title' => $title,
                    'description' => $description,
                    'file_path' => $filePath,
                    'mood' => $mood
                ]);
                $message = "Track uploaded successfully!";
            } catch (PDOException $e) {
                $message = "Error saving track: " . htmlspecialchars($e->getMessage());
            }
        } else {
            $message = "Failed to upload file.";
        }
    } else {
        $message = "Only MP3 files are allowed, and mood must be between 1 and 10.";
    }
}

// Handle track deletion
if (isset($_GET['delete_id'])) {
    $trackId = (int)$_GET['delete_id'];

    try {
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
            $message = "Track deleted successfully.";
        }
    } catch (PDOException $e) {
        $message = "Error deleting track: " . htmlspecialchars($e->getMessage());
    }
}

// Fetch meditation tracks from the database
try {
    $stmt = $pdo->query('SELECT * FROM meditation_tracks');
    $tracks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Error fetching tracks: " . htmlspecialchars($e->getMessage());
}
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
        .message {
            color: green;
            font-weight: bold;
        }
    </style>
</head>
<body>

<h1>Meditation Library</h1>

<!-- Back Button -->
 </script>

    <!-- Back Button -->
    <button onclick="goBack()">Go Back</button>

    <script>
    function goBack() {
        window.history.back();
    }
    </script>

<?php if ($message): ?>
    <p class="message"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

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
    <?php if ($tracks): ?>
        <?php foreach ($tracks as $track): ?>
            <div class="track">
                <h3><?= htmlspecialchars($track['title']) ?></h3>
                <p><?= htmlspecialchars($track['description']) ?></p>
                <p><strong>Mood:</strong> <?= htmlspecialchars($track['mood']) ?></p>
                <audio controls>
                    <source src="<?= htmlspecialchars($track['file_path']) ?>" type="audio/mpeg">
                    Your browser does not support the audio element.
                </audio>
                <form method="GET" style="display:inline;">
                    <input type="hidden" name="delete_id" value="<?= htmlspecialchars($track['id']) ?>">
                    <button type="submit">Delete Track</button>
                </form>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No tracks available.</p>
    <?php endif; ?>
</div>

</body>
</html>
