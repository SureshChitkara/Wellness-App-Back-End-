<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch mood data from the database
$stmt = $pdo->prepare("SELECT tracking_date, mood FROM meditation_tracking WHERE user_id = :user_id ORDER BY tracking_date DESC");
$stmt->execute(['user_id' => $user_id]);
$moods = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for chart
$dates = [];
$averageMoods = [];
foreach ($moods as $entry) {
    $dates[] = $entry['tracking_date'];
    $averageMoods[] = $entry['mood'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Mood Tracking Chart</title>
    <!-- Include chart library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <canvas id="moodChart" width="400" height="200"></canvas>
    <script>
        const ctx = document.getElementById('moodChart').getContext('2d');
        const moodChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($dates) ?>,
                datasets: [{
                    label: 'Mood Rating',
                    data: <?= json_encode($averageMoods) ?>,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10
                    }
                }
            }
        });
    </script>
</body>
</html>
