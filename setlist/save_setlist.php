<?php
include '../db.php';
$conn = connectToDB();

$data = json_decode(file_get_contents("php://input"), true);

$setlistName = $data['name'] ?? '';
$sets = $data['sets'] ?? [];

if ($setlistName === '') {
    http_response_code(400);
    echo "Missing setlist name.";
    exit;
}

// Step 1: Insert setlist name
$stmt = $conn->prepare("INSERT INTO setlists (name) VALUES (?)");
$stmt->bind_param("s", $setlistName);
$stmt->execute();
$setlistId = $stmt->insert_id;
$stmt->close();

// Step 2: Insert songs into setlist_songs
$stmt = $conn->prepare("
    INSERT INTO setlist_songs (setlist_id, set_number, song_id, position)
    VALUES (?, ?, ?, ?)
");

foreach ($sets as $setNumber => $songs) {
    $position = 1;
    foreach ($songs as $song) {
        $songId = $song['id'];
        $stmt->bind_param("iiii", $setlistId, $setNumber, $songId, $position);
        $stmt->execute();
        $position++;
    }
}

$stmt->close();
$conn->close();

echo "✅ Setlist '$setlistName' saved with ID $setlistId.";
