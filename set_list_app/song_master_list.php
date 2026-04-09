<div id=header_input>
<?php include 'header.php'; ?>

<div>
<h2>Add a New Song</h2>
<form method="POST">
  <label>Title: <input type="text" name="title" required></label><br>
  <label>Artist: <input type="text" name="artist" required></label><br>
  <label>Song Key: <input type="text" name="song_key"></label><br>
  <label>BPM: <input type="number" name="bpm"></label><br>
  <label>Notes: <input type="text" name="notes"></label><br>
  <button type="submit">Add Song</button>
</form>
</div>

</div>


<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// DB connection
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'rollin_ramblers_db';

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query songs
$sql = "SELECT title, artist, song_key, bpm, notes FROM songs ORDER BY artist, title";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Songs Table</title>
  <link rel="stylesheet" href="song_master_list.css">
  <style>
    body { font-family: sans-serif; padding: 1rem; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid #ccc; padding: 0.5rem; text-align: left; }
    th { background: #f4f4f4; }
    tr:nth-child(even) { background: #fafafa; }
  </style>
</head>
<body>
  <h1>Song Master List</h1>

  <?php if ($result && $result->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th>Title</th>
          <th>Artist</th>
          <th>Song Key</th>
          <th>BPM</th>
          <th>Notes</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['title']) ?></td>
            <td><?= htmlspecialchars($row['artist']) ?></td>
            <td><?= htmlspecialchars($row['song_key']) ?></td>
            <td><?= htmlspecialchars($row['bpm']) ?></td>
            <td><?= htmlspecialchars($row['notes']) ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p>No songs found in the database.</p>
  <?php endif; ?>
  <?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Grab and sanitize form inputs
    $title = $conn->real_escape_string($_POST['title']);
    $artist = $conn->real_escape_string($_POST['artist']);
    $song_key = $conn->real_escape_string($_POST['song_key']);
    $bpm = $conn->real_escape_string($_POST['bpm']);
    $notes = $conn->real_escape_string($_POST['notes']);

    // Insert into DB
    $sql = "INSERT INTO songs (title, artist, song_key, bpm, notes)
            VALUES ('$title', '$artist', '$song_key', '$bpm', '$notes')";

    if ($conn->query($sql) === TRUE) {
        echo "<p>✅ New song added successfully!</p>";
    } else {
        echo "<p>❌ Error: " . $conn->error . "</p>";
    }
}
?>



  
  
  
  <script>
  (function () {
    const table = document.querySelector('table');
    if (!table) return;
    const tbody = table.querySelector('tbody');
    const ths = table.querySelectorAll('thead th');

    // Make Title and Artist clickable (indexes 0 and 1)
    [0, 1].forEach(idx => {
      const th = ths[idx];
      th.style.cursor = 'pointer';
      th.dataset.dir = 'asc';
      th.dataset.label = th.textContent.trim();

      th.addEventListener('click', () => {
        // toggle direction
        th.dataset.dir = th.dataset.dir === 'asc' ? 'desc' : 'asc';
        const dir = th.dataset.dir;

        // clear arrows on others
        [0, 1].forEach(i => {
          ths[i].textContent = ths[i].dataset.label;
          if (i !== idx) ths[i].dataset.dir = 'asc';
        });

        // add arrow to active
        th.textContent = th.dataset.label + (dir === 'asc' ? ' ▲' : ' ▼');

        // collect and sort rows
        const rows = Array.from(tbody.rows);
        const get = (row, i) => row.cells[i].textContent.trim();

        rows.sort((ra, rb) => {
          // primary compare on clicked column (case/locale-insensitive)
          let a = get(ra, idx), b = get(rb, idx);
          let cmp = a.localeCompare(b, undefined, {sensitivity: 'base'});
          // tie-breaker on the other text column for stability
          if (cmp === 0) {
            const tieIdx = idx === 0 ? 1 : 0;
            cmp = get(ra, tieIdx).localeCompare(get(rb, tieIdx), undefined, {sensitivity: 'base'});
          }
          return dir === 'asc' ? cmp : -cmp;
        });

        // reattach in new order
        rows.forEach(r => tbody.appendChild(r));
      });
    });
  })();
</script>

</body>
</html>




