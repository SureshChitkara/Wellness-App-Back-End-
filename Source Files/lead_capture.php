<?php
// Include the database connection
include 'db_connect.php';

// Initialize variables for feedback messages
$success_message = '';
$error_message = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize and validate input
    $name = filter_var(trim($_POST['name']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);

    // Basic validation checks
    if (empty($name) || empty($email)) {
        $error_message = 'Please enter both your name and email.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        try {
            // Check for existing email in the leads table to prevent duplicate entries
            $stmt = $pdo->prepare("SELECT * FROM leads WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $existingLead = $stmt->fetch();

            if ($existingLead) {
                $error_message = 'This email is already registered. Thank you for signing up!';
            } else {
                // Insert new lead into the database
                $stmt = $pdo->prepare("INSERT INTO leads (name, email) VALUES (:name, :email)");
                $stmt->execute(['name' => $name, 'email' => $email]);

                $success_message = 'Thank you for joining our wellness community!';
            }
        } catch (PDOException $e) {
            $error_message = 'Error: Unable to submit your information. Please try again later.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Join Our Wellness Community</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f8ff; margin: 0; padding: 0; }
        .container { padding: 20px; }
        .message { margin-bottom: 20px; }
        .success { color: green; }
        .error { color: red; }
    </style>
</head>
<body>

<div class="container">
    <h2>Join Our Wellness Community</h2>

    <!-- Display success or error message -->
    <?php if (!empty($success_message)): ?>
        <div class="message success"><?php echo $success_message; ?></div>
    <?php elseif (!empty($error_message)): ?>
        <div class="message error"><?php echo $error_message; ?></div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="name">Name:</label>
        <input type="text" id="name" name="name" required>
        <br>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required>
        <br>

        <button type="submit">Submit</button>
    </form>
</div>

</body>
</html>
