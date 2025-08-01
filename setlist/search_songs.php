<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// DB connection
include '../db.php'; // Adjust path if needed
$conn = connectToDB(); // ← this is the missing piece


header('Content-Type: application/json');

// Get search query
$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if ($q === '') {
    echo json_encode([]);
    exit;
}

// Prepare query (avoid SQL injection)
$stmt = $conn->prepare("SELECT id, title AS song, artist FROM songs WHERE title LIKE ? OR artist LIKE ? LIMIT 10");


if (!$stmt) {
    die("❌ Prepare failed: " . $conn->error);
}

$likeQ = "%$q%";
$stmt->bind_param("ss", $likeQ, $likeQ);

$stmt->execute();
$result = $stmt->get_result();

$songs = [];

while ($row = $result->fetch_assoc()) {
    $songs[] = $row;
}

echo json_encode($songs);
