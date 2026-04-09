<?php
require __DIR__ . '/../config.php';
header('Content-Type: application/json; charset=utf-8');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
  // Accept form-encoded or JSON body:
  $setlist_id = 0;
  if (!empty($_POST['setlist_id'])) {
    $setlist_id = (int)$_POST['setlist_id'];
  } else {
    $raw = file_get_contents('php://input');
    if ($raw) {
      $json = json_decode($raw, true);
      if (isset($json['setlist_id'])) $setlist_id = (int)$json['setlist_id'];
    }
  }

  if ($setlist_id <= 0) {
    echo json_encode(['ok'=>false,'error'=>'Missing setlist_id']);
    exit;
  }

  $db = $config['db'];
  $conn = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);
  $conn->set_charset('utf8mb4');

  // meta
  $meta = ['name'=>null,'created_at'=>null];
  $q1 = $conn->prepare("SELECT name, created_at FROM setlists WHERE id = ?");
  $q1->bind_param('i', $setlist_id);
  $q1->execute();
  if ($r = $q1->get_result()->fetch_assoc()) {
    $meta['name'] = $r['name'] ?? null;
    $meta['created_at'] = $r['created_at'] ?? null;
  }
  $q1->close();

  // items
  $sql = "SELECT ss.set_number, ss.position, s.title, s.artist, s.song_key, s.bpm
          FROM setlist_songs ss
          JOIN songs s ON ss.song_id = s.id
          WHERE ss.setlist_id = ?
          ORDER BY ss.set_number ASC, ss.position ASC";
  $q2 = $conn->prepare($sql);
  $q2->bind_param('i', $setlist_id);
  $q2->execute();

  $items = [];
  $res = $q2->get_result();
  while ($row = $res->fetch_assoc()) { $items[] = $row; }
  $q2->close();

  $conn->close();

  echo json_encode(['ok'=>true, 'meta'=>$meta, 'items'=>$items], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
  if ($config['debug']) {
    echo json_encode(['ok'=>false,'error'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
  } else {
    error_log($e);
    echo json_encode(['ok'=>false,'error'=>'Server error']);
  }
}
