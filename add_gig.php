<?php
include 'db.php';
$conn = connectToDB();

$success = false;
$error = '';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $date = $_POST['gig_date'] ?? '';
    $event = $_POST['event_name'] ?? '';
    $venue = $_POST['venue'] ?? '';
    $map = $_POST['map_url'] ?? '';

    if ($date && $event && $venue) {
        $stmt = $conn->prepare("INSERT INTO gigs (gig_date, event_name, venue, map_url) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssss", $date, $event, $venue, $map);
        $success = $stmt->execute();
        if (!$success) {
            $error = "Insert failed: " . $stmt->error;
        }
    } else {
        $error = "Date, event name, and venue are required.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Gig</title>
    <style>
        form {
            width: 60%;
            margin: 2rem auto;
            padding: 1rem;
            border: 1px solid #ccc;
        }
        label {
            display: block;
            margin-top: 1rem;
        }
        input[type="text"],
        input[type="date"] {
            width: 100%;
            padding: 8px;
        }
        .success {
            color: green;
            text-align: center;
        }
        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <h1 style="text-align: center;">Add New Gig</h1>
    
    <?php if ($success): ?>
        <p class="success">Gig added successfully!</p>
    <?php elseif ($error): ?>
        <p class="error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="post" action="">
        <label for="gig_date">Date:</label>
        <input type="date" name="gig_date" required>

        <label for="event_name">Event Name:</label>
        <input type="text" name="event_name" required>

        <label for="venue">Venue:</label>
        <input type="text" name="venue" required>

        <label for="map_url">Map URL (optional):</label>
        <input type="text" name="map_url">

        <button type="submit" style="margin-top: 1rem;">Add Gig</button>
    </form>
</body>
</html>
