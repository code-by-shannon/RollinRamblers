<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'rollin_ramblers_db';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Get setlist ID from URL
$setlist_id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if (!$setlist_id) {
    die("No setlist ID provided.");
}

// Optional: Get the name of the setlist
$name_query = $conn->prepare("SELECT name FROM setlists WHERE id = ?");
$name_query->bind_param("i", $setlist_id);
$name_query->execute();
$name_result = $name_query->get_result();
$setlist_name = $name_result->num_rows ? $name_result->fetch_assoc()['name'] : 'Unknown Setlist';

// Query the songs in the setlist
$sql = "
SELECT 
    ss.set_number,
    ss.position,
    s.title,
    s.artist,
    s.song_key,
    s.bpm,
    s.notes
FROM 
    setlist_songs ss
JOIN 
    songs s ON ss.song_id = s.id
WHERE 
    ss.setlist_id = ?
ORDER BY 
    ss.set_number ASC, ss.position ASC
";


$stmt = $conn->prepare($sql);
if (!$stmt) {
  die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $setlist_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($setlist_name) ?></title>
  <link rel="stylesheet" href="end_user_set_list_style.css">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
</head>
<body>

<h1><?= htmlspecialchars($setlist_name) ?></h1>
<h2>Set 1</h2>

<?php if ($result && $result->num_rows > 0): ?>
  <table>
    <tbody>
      <?php while ($row = $result->fetch_assoc()): ?>
        <?php $has_notes = !empty($row['notes']); ?>

        <!-- Line 1: Title + Artist -->
<tr>
  <td class="title-artist">
    <strong><?= htmlspecialchars($row['title']) ?></strong> – <?= htmlspecialchars($row['artist']) ?>
  </td>
</tr>

<!-- Line 2: Key + BPM -->
<tr>
  <td class="key-bpm">
    <span><strong>Key:</strong> <?= htmlspecialchars($row['song_key']) ?></span>
    <span><strong>BPM:     </strong><button class="bpm-cell bpm-button" data-bpm="<?= htmlspecialchars($row['bpm']) ?>">
  <?= htmlspecialchars($row['bpm']) ?> BPM
</button>

  </td>
</tr>


        <!-- Line 3: Notes (optional) -->
        <?php if ($has_notes): ?>
          <tr class="highlighted-row">
            <td colspan="2" class="notes-cell">
              <strong>Notes:</strong> <?= htmlspecialchars($row['notes']) ?>
            </td>
          </tr>
        <?php endif; ?>
      <?php endwhile; ?>
    </tbody>
  </table>
<?php else: ?>
  <p>No songs found in this setlist.</p>
<?php endif; ?>


<script src="metronome_logic.js"></script>
</body>

</body>
</html>
