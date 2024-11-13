<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    // Redirect if not logged in
    header('Location: index.php');
    exit();
}

// Retrieve user_id from session
$user_id = $_SESSION['user_id'];

// Fetch mood data grouped by date, with average mood per day
$stmt = $pdo->prepare("SELECT DATE(tracking_date) as date, AVG(mood) as average_mood 
                       FROM meditation_tracking 
                       WHERE user_id = :user_id 
                       GROUP BY DATE(tracking_date) 
                       ORDER BY date DESC");
$stmt->execute(['user_id' => $user_id]);
$moods = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Prepare data for chart
$dates = [];
$averageMoods = [];
foreach ($moods as $entry) {
    $dates[] = $entry['date'];
    $averageMoods[] = round($entry['average_mood'], 2); // Round to 2 decimal places for clarity
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Mood Tracking Chart</title>
    <!-- Include chart library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f8ff;
            padding: 20px;
        }
        canvas {
            max-width: 100%;
            margin: 20px auto;
            display: block;
        }
        button {
            padding: 10px 20px;
            background-color: #9c28c2;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <h1>Mood Tracking Chart</h1>
    <canvas id="moodChart" width="400" height="200"></canvas>

    <script>
        const ctx = document.getElementById('moodChart').getContext('2d');
        const moodChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: <?= json_encode($dates) ?>,
                datasets: [{
                    label: 'Average Mood Rating',
                    data: <?= json_encode($averageMoods) ?>,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 2,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    title: {
                        display: true,
                        text: 'Daily Average Mood Over Time'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 10,
                        title: {
                            display: true,
                            text: 'Mood Rating'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    }
                }
            }
        });
    </script>

    <!-- Back Button -->
    <button onclick="goBack()">Go Back</button>

    <script>
    function goBack() {
        window.history.back();
    }
    </script>
</body>
</html>
