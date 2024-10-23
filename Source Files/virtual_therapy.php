<?php 
// Include the database connection
include 'db_connect.php';

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $therapist_id = $_POST['therapist_id'];
    $appointment_time = $_POST['appointment_time'];

    // Insert appointment into the database
    $stmt = $pdo->prepare("INSERT INTO appointments (therapist_id, appointment_time) VALUES (:therapist_id, :appointment_time)");
    $stmt->execute(['therapist_id' => $therapist_id, 'appointment_time' => $appointment_time]);

    // Confirmation message
    $confirmation_message = "Your appointment has been successfully scheduled.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Virtual Therapy - Schedule an Appointment</title>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f0f8ff; margin: 0; padding: 0; }
        header { background-color: #9c28c2; padding: 20px; color: white; text-align: center; }
        .container { padding: 20px; }
        .confirmation { color: green; margin-bottom: 20px; }
        form { background-color: #e0e0e0; padding: 20px; border-radius: 10px; }
        form input, form select, form button { padding: 10px; margin: 5px 0; width: 100%; }
    </style>
</head>
<body>

<header>
    <h1>Virtual Therapy</h1>
    <p>Schedule your appointment with one of our therapists.</p>
</header>
<!-- Back Button -->
<button onclick="goBack()">Go Back</button>

<script>
function goBack() {
    window.history.back();
}
</script>
<div class="container">
    <?php if (isset($confirmation_message)): ?>
        <div class="confirmation">
            <?php echo $confirmation_message; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <label for="therapist_id">Select Therapist:</label>
        <select name="therapist_id" required>
            <option value="">-- Choose a Therapist --</option>
            <?php
            // Fetch therapists from the database
            $stmt = $pdo->query('SELECT * FROM therapists');
            $therapists = $stmt->fetchAll();

            foreach ($therapists as $therapist) {
                echo "<option value=\"" . $therapist['id'] . "\">" . $therapist['name'] . " - " . $therapist['specialty'] . "</option>";
            }
            ?>
        </select>

        <label for="appointment_time">Select Appointment Time:</label>
        <input type="datetime-local" name="appointment_time" required>

        <button type="submit">Schedule Appointment</button>
    </form>
</div>

</body>
</html>
