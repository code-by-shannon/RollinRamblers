<?php
// DB connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'rollin_ramblers_db';
$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = $_POST['title'];
    $artist = $_POST['artist'];
    $key = $_POST['song_key'];
    $bpm = $_POST['bpm'];
    $notes = $_POST['notes'];

    $stmt = $conn->prepare("INSERT INTO songs (title, artist, song_key, bpm, notes) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $title, $artist, $key, $bpm, $notes);
    if ($stmt->execute()) {
        echo "🎵 Song added successfully!";
    } else {
        echo "❌ Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<h2>Add a Song</h2>
<form method="post">
    <label>Title: <input type="text" name="title" required></label><br>
    <label>Artist: <input type="text" name="artist"></label><br>
    <label>Key: <input type="text" name="song_key" maxlength="5"></label><br>
    <label>BPM: <input type="number" name="bpm" min="0"></label><br>
    <label>Notes:<br><textarea name="notes" rows="4" cols="40"></textarea></label><br>
    <button type="submit">Add Song</button>
</form>
