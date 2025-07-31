<?php
include 'db.php';
$conn = connectToDB();

$id = $_GET['id'] ?? null;

if ($id) {
    $stmt = $conn->prepare("DELETE FROM gigs WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
}

// Redirect back to the admin page after deleting
header("Location: gig_admin.php");
exit;
