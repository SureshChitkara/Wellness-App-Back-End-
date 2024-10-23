<?php
// Include database connection
include 'db_connect.php';

// Fetch mood data from the database
$stmt = $pdo->query('SELECT * FROM moods');
$moods = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mood Tracking</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        .chart-container {
            width: 80%;
            margin: 0 auto;
        }
    </style>
</head>
<body>

<h1>Mood Tracking</h1>

<div class="chart-container">
    <canvas id="moodChart"></canvas>
</div>
<!-- Back Button -->
<button onclick="goBack()">Go Back</button>

<script>
function goBack() {
    window.history.back();
}
</script>
<script>
    // Prepare the mood data for the chart
    const moodData = <?php echo json_encode($moods); ?>;

    // Extracting mood levels and timestamps
    const labels = moodData.map(mood => new Date(mood.created_at).toLocaleDateString());
    const data = moodData.map(mood => mood.mood_level);

    const ctx = document.getElementById('moodChart').getContext('2d');
    const moodChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [{
                label: 'Mood Level',
                data: data,
                borderColor: '#4a90e2',
                backgroundColor: 'rgba(74, 144, 226, 0.2)',
                borderWidth: 2,
                fill: true,
                tension: 0.1
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
