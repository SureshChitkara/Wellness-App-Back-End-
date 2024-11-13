<?php
session_start();
include 'db_connect.php';

$message = '';

// Process the form submission for registration or login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);

    // Check if user exists in the leads table
    $stmt = $pdo->prepare('SELECT * FROM leads WHERE email = :email');
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        // Login existing user
        $_SESSION['user_id'] = $user['id'];
        $message = "Welcome back, " . htmlspecialchars($user['name']) . "!";
        header("Location: index.php");
        exit();
    } else {
        // Register new user
        $stmt = $pdo->prepare('INSERT INTO leads (name, email) VALUES (:name, :email)');
        $stmt->execute(['name' => $name, 'email' => $email]);
        $_SESSION['user_id'] = $pdo->lastInsertId();
        $message = "Registration successful! Welcome, " . htmlspecialchars($name) . "!";
        header("Location: index.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Wellness App - Home</title>
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
        .login-form, .section {
            margin-bottom: 20px;
        }
        .login-form input, .login-form button {
            padding: 10px;
            margin: 5px 0;
            width: 100%;
        }
        footer {
            background-color: #9c28c2;
            padding: 20px;
            color: white;
            text-align: center;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>

<header>
    <h1>Welcome to the Wellness App</h1>
    <p>Your journey to mindfulness and wellness begins here.</p>
</header>

<div class="container">

    <?php if (isset($message)): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if (!isset($_SESSION['user_id'])): ?>
    <!-- Login and Registration Form -->
    <div class="login-form">
        <h2>Log in or Join Our Wellness Community</h2>
        <form method="POST" action="">
            <input type="text" name="name" placeholder="Your Name" required>
            <input type="email" name="email" placeholder="Your Email" required>
            <button type="submit">Submit</button>
        </form>
    </div>
    <?php else: ?>
    <!-- Logout Button -->
    <div style="text-align: right;">
        <form method="POST" action="logout.php">
            <button type="submit">Logout</button>
        </form>
    </div>
    <?php endif; ?>

    <!-- Therapist Listing Section -->
    <div class="section">
        <h2>Meet Our Therapists</h2>
        <div>
            <?php
            try {
                // Fetch therapists from the database
                $stmt = $pdo->query('SELECT * FROM therapists');
                $therapists = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if ($therapists) {
                    foreach ($therapists as $therapist) {
                        echo "<div><strong>" . htmlspecialchars($therapist['name']) . "</strong> - " . htmlspecialchars($therapist['specialty']) . "</div>";
                    }
                } else {
                    echo "No therapists found.";
                }
            } catch (PDOException $e) {
                echo "Error fetching therapists: " . $e->getMessage();
            }
            ?>
        </div>
    </div>

    <!-- Navigation Links to Other Features -->
    <div class="section">
        <h2>Explore Our Features</h2>
        <ul>
            <li><a href="meditation_library.php">Meditation Library</a></li>
            <li><a href="mood_tracking.php">Mood Tracking</a></li>
            <li><a href="virtual_therapy.php">Virtual Therapy</a></li>
            <li><a href="therapists.php">Add a Therapist</a></li>
        </ul>
    </div>
</div>

<footer>
    <p>&copy; 2024 Wellness App. All rights reserved.</p>
</footer>

</body>
</html>
