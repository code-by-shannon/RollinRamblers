<?php
require __DIR__ . '/../config.php';
header('Content-Type: application/json; charset=utf-8');

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
  $db = $config['db'];
  $conn = new mysqli($db['host'], $db['user'], $db['pass'], $db['name']);
  $conn->set_charset('utf8mb4');

  $sql = "SELECT id, name, created_at FROM setlists ORDER BY created_at DESC";
  $res = $conn->query($sql);

  $rows = [];
  while ($r = $res->fetch_assoc()) { $rows[] = $r; }
  $conn->close();

  echo json_encode(['ok'=>true, 'setlists'=>$rows], JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
} catch (Throwable $e) {
  if ($config['debug']) {
    echo json_encode(['ok'=>false,'error'=>$e->getMessage()], JSON_UNESCAPED_UNICODE);
  } else {
    error_log($e);
    echo json_encode(['ok'=>false,'error'=>'Server error']);
  }
}
