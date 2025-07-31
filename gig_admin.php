<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';
$conn = connectToDB();

$result = $conn->query("SELECT * FROM gigs ORDER BY gig_date ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Gigs Admin</title>
    <style>
        table {
            border-collapse: collapse;
            width: 90%;
            margin: 2rem auto;
        }
        th, td {
            padding: 12px;
            border: 1px solid #ccc;
        }
        th {
            background-color: #f4f4f4;
        }
        h1 {
            text-align: center;
        }
    </style>
</head>
<body>
    <h1>Rollin' Ramblers Gigs</h1>

    <table>
        <tr>
            <th>Date</th>
            <th>Event</th>
            <th>Venue</th>
            <th>Map</th>
            <th>Actions</th>
        </tr>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['gig_date']) ?></td>
                    <td><?= htmlspecialchars($row['event_name']) ?></td>
                    <td><?= htmlspecialchars($row['venue']) ?></td>
                    <td>
                        <?php if (!empty($row['map_url'])): ?>
                            <a href="<?= htmlspecialchars($row['map_url']) ?>" target="_blank">Map</a>
                        <?php else: ?>
                            —
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="edit_gig.php?id=<?= $row['id'] ?>">Edit</a> |
                        <a href="delete_gig.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this gig?');">Delete</a>
                    </td>
                </tr>
                <!-- Debug line to see the row structure -->
                <!-- <pre><?php print_r($row); ?></pre> -->
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5" style="text-align:center;">No gigs found in the database.</td></tr>
        <?php endif; ?>
    </table>

</body>
</html>
