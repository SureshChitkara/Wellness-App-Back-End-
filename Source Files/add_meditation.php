<?php
include 'db_connect.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $track_id = $_POST['track_id'];
    $mood = $_POST['mood'];

    // Insert the mood tracking data into the database
    $stmt = $pdo->prepare("INSERT INTO meditation_tracking (user_id, track_id, mood, tracking_date) VALUES (:user_id, :track_id, :mood, NOW())");
    $stmt->execute(['user_id' => $user_id, 'track_id' => $track_id, 'mood' => $mood]);

    // Redirect to the dashboard or wherever you need after tracking mood
    header("Location: dashboard.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Add Meditation Entry</title>
</head>
<body>
    <form action="add_meditation.php" method="post">
        <input type="number" name="track_id" placeholder="Track ID" required>
        <input type="number" name="mood" placeholder="Mood (1-10)" required>
        <button type="submit">Add Entry</button>
    </form>
</body>
</html>
