<?php
// Enable error reporting
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Connect to DB
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'rollin_ramblers_db';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query all songs
$sql = "SELECT id, title, artist FROM songs ORDER BY id ASC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Master Song List</title>
  <style>
    body {
      font-family: sans-serif;
      padding: 2rem;
      background-color: #f4f4f4;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 1rem;
    }

    th, td {
      border: 1px solid #aaa;
      padding: 0.75rem;
      text-align: left;
    }

    th {
      background-color: #333;
      color: #fff;
    }

    tr:nth-child(even) {
      background-color: #eee;
    }
  </style>
</head>
<body>
  <h1>Master Song List</h1>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Title</th>
        <th>Artist</th>
      </tr>
    </thead>
    <tbody>
      <?php if ($result && $result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['id']) ?></td>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['artist']) ?></td>
          </tr>
        <?php endwhile; ?>
      <?php else: ?>
        <tr><td colspan="3">No songs found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</body>
</html>

