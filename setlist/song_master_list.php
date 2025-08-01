<?php
include '../db.php'; // adjust if needed
$conn = connectToDB();

$result = $conn->query("SELECT artist, title FROM songs ORDER BY title ASC");

while ($row = $result->fetch_assoc()) {
    echo htmlspecialchars($row['title']) . " – " . htmlspecialchars($row['artist']) . "<br>";
}
?>
