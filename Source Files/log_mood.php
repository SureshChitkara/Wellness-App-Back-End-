<?php
session_start();
include 'db_connect.php';

header('Content-Type: application/json');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit();
}

// Get data from the request
$data = json_decode(file_get_contents('php://input'), true);
$user_id = $_SESSION['user_id'];
$track_id = $data['track_id'];
$mood = $data['mood'];

try {
    // Insert the mood data for the track, recording the date and time
    $stmt = $pdo->prepare("INSERT INTO meditation_tracking (user_id, track_id, mood, tracking_date) 
                           VALUES (:user_id, :track_id, :mood, NOW())");
    $stmt->execute([
        'user_id' => $user_id,
        'track_id' => $track_id,
        'mood' => $mood
    ]);

    // Update user's current mood in the user profile table (optional if needed for mood persistence)
    $updateMoodStmt = $pdo->prepare("UPDATE leads SET current_mood = :mood WHERE id = :user_id");
    $updateMoodStmt->execute([
        'mood' => $mood,
        'user_id' => $user_id
    ]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
