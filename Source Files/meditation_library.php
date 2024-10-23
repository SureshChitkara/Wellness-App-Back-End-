<?php 
// Include the database connection
include 'db_connect.php';

// Pagination setup
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$tracks_per_page = 5;
$offset = ($page - 1) * $tracks_per_page;

// Fetch meditation tracks with pagination
$stmt = $pdo->prepare('SELECT * FROM meditation_tracks LIMIT :limit OFFSET :offset');
$stmt->bindValue(':limit', $tracks_per_page, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$tracks = $stmt->fetchAll();

// Count total meditation tracks for pagination
$total_tracks_stmt = $pdo->query('SELECT COUNT(*) FROM meditation_tracks');
$total_tracks = $total_tracks_stmt->fetchColumn();
$total_pages = ceil($total_tracks / $tracks_per_page);
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
        .pagination {
            margin-top: 20px;
            text-align: center;
        }
        .pagination a {
            text-decoration: none;
            color: #9c28c2;
            padding: 10px;
            margin: 0 5px;
            border: 1px solid #9c28c2;
            border-radius: 5px;
        }
        .pagination a:hover {
            background-color: #9c28c2;
            color: white;
        }
    </style>
</head>
<body>

<!-- Back Button -->
<button onclick="goBack()">Go Back</button>

<script>
function goBack() {
    window.history.back();
}
</script>

<header>
    <h1>Meditation Library</h1>
    <p>Relax and explore a variety of meditation tracks.</p>
</header>

<div class="container">
    <?php
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
    
    <!-- Pagination -->
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
        <?php endif; ?>

        <?php if ($page < $total_pages): ?>
            <a href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
        <?php endif; ?>
    </div>
</div>

<footer>
    <p>&copy; 2024 Wellness App. All rights reserved.</p>
</footer>

</body>
</html>
