<?php
// Include the database connection
include 'db_connect.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $specialty = $_POST['specialty'];
    $bio = $_POST['bio'];
    $profile_picture = ''; // Optional: Set a default image path or handle file uploads

    // Insert therapist into the database
    $stmt = $pdo->prepare("INSERT INTO therapists (name, specialty, bio, profile_picture) VALUES (:name, :specialty, :bio, :profile_picture)");
    $stmt->execute([
        'name' => $name,
        'specialty' => $specialty,
        'bio' => $bio,
        'profile_picture' => $profile_picture
    ]);

    // Confirmation message
    $confirmation_message = "Therapist added successfully.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Therapists</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f8ff; padding: 20px; }
        .form-container { margin-bottom: 20px; }
        .confirmation { color: green; margin-bottom: 20px; }
        form { background-color: #e0e0e0; padding: 20px; border-radius: 10px; }
        form input, form textarea, form button { padding: 10px; margin: 5px 0; width: 100%; }
        .therapist-list { background-color: #ffffff; padding: 20px; border-radius: 10px; }
        .therapist { margin-bottom: 20px; padding: 15px; background-color: #e0e0e0; border-radius: 10px; }
    </style>
</head>
<body>

<h1>Add a New Therapist</h1>

<!-- Confirmation Message -->
<?php if (isset($confirmation_message)): ?>
    <div class="confirmation">
        <?php echo $confirmation_message; ?>
    </div>
<?php endif; ?>

<!-- Therapist Form -->
<div class="form-container">
    <form method="POST" action="">
        <label for="name">Therapist Name:</label>
        <input type="text" name="name" id="name" required>

        <label for="specialty">Specialty:</label>
        <input type="text" name="specialty" id="specialty" required>

        <label for="bio">Bio:</label>
        <textarea name="bio" id="bio" required></textarea>

        <button type="submit">Add Therapist</button>
    </form>
</div>

<h2>Current Therapists</h2>
<div class="therapist-list">
    <?php
    // Fetch all therapists from the database
    $stmt = $pdo->query('SELECT * FROM therapists');
    $therapists = $stmt->fetchAll();

    if (count($therapists) > 0) {
        foreach ($therapists as $therapist) {
            echo "<div class='therapist'>";
            echo "<h3>" . htmlspecialchars($therapist['name']) . "</h3>";
            echo "<p><strong>Specialty:</strong> " . htmlspecialchars($therapist['specialty']) . "</p>";
            echo "<p>" . htmlspecialchars($therapist['bio']) . "</p>";
            echo "</div>";
        }
    } else {
        echo "<p>No therapists available at the moment. Please add new therapists.</p>";
    }
    ?>
</div>

</body>
</html>
