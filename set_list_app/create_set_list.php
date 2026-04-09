<?php
// ------------------------
// Backend helpers
// ------------------------
function db() {
  // --- SiteGround creds (primary) ---
  $sg = [
      'host'   => 'localhost',
      'user'   => 'ukovkzw7w81g8',
      'pass'   => 'tc2*v5@11|14',
      'dbname' => 'dbyewaz01eteo0'
  ];

  // --- Local creds (fallback) ---
  $local = [
      'host'   => 'localhost',
      'user'   => 'root',
      'pass'   => '',
      'dbname' => 'rollin_ramblers_db'
  ];

  // Try SiteGround first
  $conn = @new mysqli($sg['host'], $sg['user'], $sg['pass'], $sg['dbname']);

  if ($conn->connect_error) {
      // Failover to local
      $conn = @new mysqli($local['host'], $local['user'], $local['pass'], $local['dbname']);
  }

  // Still failing?
  if ($conn->connect_error) {
      http_response_code(500);
      header('Content-Type: application/json');
      echo json_encode(['ok' => false, 'error' => 'DB connection failed: ' . $conn->connect_error]);
      exit;
  }

  $conn->set_charset('utf8mb4');
  return $conn;
}


function json_out($arr) {
    header('Content-Type: application/json');
    echo json_encode($arr);
    exit;
}

// ------------------------
// Backend: AJAX handlers
// ------------------------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'create_setlist') {
        $name = trim($_POST['name'] ?? '');
        if ($name === '') json_out(['ok' => false, 'error' => 'Please provide a setlist name.']);

        $conn = db();
        $stmt = $conn->prepare("INSERT INTO setlists (name, created_at) VALUES (?, NOW())");
        if (!$stmt) json_out(['ok' => false, 'error' => 'Prepare failed: ' . $conn->error]);
        $stmt->bind_param("s", $name);
        if (!$stmt->execute()) json_out(['ok' => false, 'error' => 'Insert failed: ' . $stmt->error]);
        $new_id = $stmt->insert_id;
        $stmt->close();
        $conn->close();
        json_out(['ok' => true, 'setlist_id' => $new_id, 'name' => $name]);
    }

    if ($action === 'list_setlists') {
      $conn = db();
      $rows = [];
      $res = $conn->query("SELECT id, name, created_at FROM setlists ORDER BY created_at DESC");
      if ($res) while ($r = $res->fetch_assoc()) $rows[] = $r;
      $conn->close();
      json_out(['ok' => true, 'setlists' => $rows]);
  }
  

    if ($action === 'get_songs') {
        $conn = db();
        $sql = "SELECT id, title, artist, song_key, bpm, notes FROM songs ORDER BY title ASC";
        $res = $conn->query($sql);
        $rows = [];
        if ($res) while ($r = $res->fetch_assoc()) $rows[] = $r;
        $conn->close();
        json_out(['ok' => true, 'songs' => $rows]);
    }

    if ($action === 'save_setlist_songs') {
        $setlist_id = intval($_POST['setlist_id'] ?? 0);
        $items_json = $_POST['items'] ?? '[]';
        if ($setlist_id <= 0) json_out(['ok' => false, 'error' => 'Missing setlist_id']);
        $items = json_decode($items_json, true);
        if (!is_array($items)) json_out(['ok' => false, 'error' => 'Bad items payload']);

        $conn = db();
        // Clear existing rows for this setlist
        $del = $conn->prepare("DELETE FROM setlist_songs WHERE setlist_id = ?");
        $del->bind_param("i", $setlist_id);
        if (!$del->execute()) json_out(['ok' => false, 'error' => 'Delete failed: ' . $del->error]);
        $del->close();

        if (!empty($items)) {
            $ins = $conn->prepare("INSERT INTO setlist_songs (setlist_id, set_number, song_id, position) VALUES (?, ?, ?, ?)");
            if (!$ins) json_out(['ok' => false, 'error' => 'Prepare insert failed: ' . $conn->error]);

            foreach ($items as $it) {
                $song_id = intval($it['song_id'] ?? 0);
                $position = intval($it['position'] ?? 0);
                $set_number = intval($it['set_number'] ?? 1); // default Set 1
                if ($song_id <= 0) continue;
                $ins->bind_param("iiii", $setlist_id, $set_number, $song_id, $position);
                if (!$ins->execute()) {
                    $err = $ins->error;
                    $ins->close(); $conn->close();
                    json_out(['ok' => false, 'error' => 'Insert failed: ' . $err]);
                }
            }
            $ins->close();
        }

        $conn->close();
        json_out(['ok' => true]);
    }

    if ($action === 'get_setlist_detail') {
      $setlist_id = intval($_POST['setlist_id'] ?? 0);
      if ($setlist_id <= 0) json_out(['ok' => false, 'error' => 'Missing setlist_id']);
  
      $conn = db();
  
      // Get name + created_at in a meta object
      $meta = ['name' => null, 'created_at' => null];
      $q1 = $conn->prepare("SELECT name, created_at FROM setlists WHERE id = ?");
      $q1->bind_param("i", $setlist_id);
      if ($q1->execute()) {
          $r = $q1->get_result()->fetch_assoc();
          if ($r) $meta = ['name' => $r['name'] ?? null, 'created_at' => $r['created_at'] ?? null];
      }
      $q1->close();
  
      // Get items joined to songs
      $sql = "SELECT ss.set_number, ss.position, s.id AS song_id, s.title, s.artist, s.song_key, s.bpm
              FROM setlist_songs ss
              JOIN songs s ON ss.song_id = s.id
              WHERE ss.setlist_id = ?
              ORDER BY ss.set_number ASC, ss.position ASC";
      $q2 = $conn->prepare($sql);
      $q2->bind_param("i", $setlist_id);
      $items = [];
      if ($q2->execute()) {
          $res = $q2->get_result();
          while ($row = $res->fetch_assoc()) $items[] = $row;
      }
      $q2->close();
      $conn->close();
  
      json_out(['ok' => true, 'meta' => $meta, 'items' => $items]);
  }
  

    // Unknown action
    json_out(['ok' => false, 'error' => 'Unknown action']);
}
include 'header.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Create Setlist</title>
<style>
  :root { color-scheme: dark; }
  body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin: 0; padding: 24px; background:#0b0b0b; color:#eaeaea; }
  h1 { margin-top: 0; }
  .actions { display:flex; gap:12px; margin: 12px 0 24px; flex-wrap: wrap; }
  .btn { background:#2f6fed; color:white; border:none; padding:10px 14px; border-radius:8px; cursor:pointer; font-weight:600; }
  .btn.secondary { background:#444; }
  .btn:disabled { opacity: .6; cursor: not-allowed; }
  .card { background:#141414; border:1px solid #2a2a2a; border-radius:12px; padding:16px; }
  .muted { color:#aaa; }
  .row { display:flex; align-items:center; gap:10px; }
  .pill { display:inline-block; background:#222; border:1px solid #2a2a2a; padding:6px 10px; border-radius:999px; }
  /* Modal */
  .modal-backdrop { position:fixed; inset:0; background:rgba(0,0,0,.65); display:none; align-items:center; justify-content:center; z-index:50; }
  .modal { width:min(520px, 92vw); background:#141414; border:1px solid #2a2a2a; border-radius:14px; padding:18px; box-shadow:0 10px 40px rgba(0,0,0,.5); }
  .modal h2 { margin:0 0 12px; }
  .modal .field { display:flex; flex-direction:column; gap:6px; margin-bottom:14px; }
  .modal input[type="text"] { width:100%; padding:10px 12px; background:#0f0f0f; color:#eaeaea; border:1px solid #2a2a2a; border-radius:8px; }
  .modal .row-right { display:flex; gap:10px; justify-content:flex-end; margin-top:10px; }
  .notice { margin-top: 12px; color:#8fd18f; display:none; }
  .error  { margin-top: 12px; color:#ff8e8e; display:none; }
  /* Layout for builder */
  .builder { display:grid; grid-template-columns: 1fr 1fr; gap:16px; margin-top:16px; }
  @media (max-width: 900px) { .builder { grid-template-columns: 1fr; } }
  .panel { background:#111; border:1px solid #2a2a2a; border-radius:12px; overflow:hidden; }
  .panel h3 { margin:0; padding:12px 14px; border-bottom:1px solid #232323; background:#141414; }
  .panel .content { max-height: 65vh; overflow:auto; }
  .song-row, .set-row {
    display:grid; grid-template-columns: 1fr auto auto auto; gap:10px;
    align-items:center; padding:10px 12px; border-bottom:1px solid #1f1f1f;
  }
  .song-row:hover { background:#161616; }
  .dim { opacity: .5; }
  .badge { font-size:12px; color:#bdbdbd; }
  .icon-btn { background:#262626; border:1px solid #333; padding:6px 8px; border-radius:8px; cursor:pointer; }
  .icon-btn:hover { background:#2e2e2e; }
  .header-actions { display:flex; gap:8px; align-items:center; }
  .sticky-footer { position:sticky; bottom:0; background:#141414; padding:10px; border-top:1px solid #232323; text-align:right; }
</style>
</head>
<body>
  <h1>Create a New Setlist</h1>

  <div class="actions">
    <button id="btnCreate" class="btn">Create New Setlist</button>
    <button id="btnLoad" class="btn secondary" disabled title="Coming soon">Load Existing</button>
  </div>

  <div id="state" class="card">
    <div class="row">
      <div class="pill">Status</div>
      <div id="statusText" class="muted">No setlist yet.</div>
    </div>
    <div id="currentSetlistHeader" style="margin-top:12px; display:none;">
      <div><strong>Setlist:</strong> <span id="setlistName"></span></div>
      <div><strong>ID:</strong> <span id="setlistId"></span></div>
    </div>
    <div id="notice" class="notice"></div>
    <div id="error" class="error"></div>
  </div>

  <!-- Builder panels -->
  <div id="builder" class="builder" style="display:none;">

    <!-- Left: Current Setlist -->
    <div class="panel" id="panelSetlist">
      <h3>Current Setlist
        <span class="badge" id="countBadge">(0 songs)</span>
      </h3>
      <div id="setlistRows" class="content"></div>
      <div class="sticky-footer">
        <button id="btnSave" class="btn">Save Setlist</button>
      </div>
    </div>

    <!-- Right: Song Picker -->
    <div class="panel" id="panelPicker">
      <h3 class="header-actions">
        Song Picker
        <span class="badge" id="totalSongs">(…)</span>
      </h3>
      <div id="pickerRows" class="content"></div>
    </div>
  </div>

  <!-- Modal -->
  <div id="backdrop" class="modal-backdrop" role="dialog" aria-modal="true" aria-hidden="true">
    <div class="modal">
      <h2>Name your setlist</h2>
      <form id="formNew" class="form" autocomplete="off">
        <div class="field">
          <label for="setlistNameInput">Setlist name</label>
          <input id="setlistNameInput" type="text" name="name" placeholder="e.g., Frank’s Tavern (Nov 2)" required />
        </div>
        <div class="row-right">
          <button type="button" id="btnCancel" class="btn secondary">Cancel</button>
          <button type="submit" id="btnSaveModal" class="btn">Save</button>
        </div>
      </form>
    </div>
  </div>

<script>
(function () {
  const btnCreate = document.getElementById('btnCreate');
  const backdrop = document.getElementById('backdrop');
  const formNew = document.getElementById('formNew');
  const inputName = document.getElementById('setlistNameInput');
  const btnCancel = document.getElementById('btnCancel');
  const statusText = document.getElementById('statusText');
  const headerWrap = document.getElementById('currentSetlistHeader');
  const spanName = document.getElementById('setlistName');
  const spanId = document.getElementById('setlistId');
  const notice = document.getElementById('notice');
  const errorBox = document.getElementById('error');

  // Builder elements
  const builder = document.getElementById('builder');
  const pickerRows = document.getElementById('pickerRows');
  const setlistRows = document.getElementById('setlistRows');
  const totalSongs = document.getElementById('totalSongs');
  const countBadge = document.getElementById('countBadge');
  const btnSave = document.getElementById('btnSave');

  let SETLIST_ID = null;
  let SONGS = [];          // all songs from DB
  let CURRENT = [];        // [{song_id, position, set_number:1}]
  const SET_NUMBER = 1;    // MVP: single set

  // ---------- Modal helpers ----------
  function openModal() {
    inputName.value = '';
    backdrop.style.display = 'flex';
    backdrop.setAttribute('aria-hidden', 'false');
    setTimeout(() => inputName.focus(), 50);
  }
  function closeModal() {
    backdrop.style.display = 'none';
    backdrop.setAttribute('aria-hidden', 'true');
  }

  btnCreate.addEventListener('click', openModal);
  btnCancel.addEventListener('click', closeModal);
  backdrop.addEventListener('click', (e) => { if (e.target === backdrop) closeModal(); });

  // ---------- AJAX helpers ----------
  async function post(action, body = {}) {
    const params = new URLSearchParams({ action, ...body });
    const res = await fetch(location.href, {
      method: 'POST',
      headers: {'Content-Type': 'application/x-www-form-urlencoded'},
      body: params
    });
    const data = await res.json();
    if (!data.ok) throw new Error(data.error || 'Unknown error');
    return data;
  }

  // ---------- UI: Renderers ----------
  function renderPicker() {
    pickerRows.innerHTML = '';
    totalSongs.textContent = `(${SONGS.length} songs)`;
    const inCurrent = new Set(CURRENT.map(it => it.song_id));

    // Simple A→Z headers
    let lastLetter = '';
    for (const s of SONGS) {
      const first = (s.title || '').trim().charAt(0).toUpperCase();
      if (first && first !== lastLetter) {
        const hdr = document.createElement('div');
        hdr.style.padding = '6px 12px';
        hdr.style.background = '#181818';
        hdr.style.borderBottom = '1px solid #252525';
        hdr.textContent = first;
        pickerRows.appendChild(hdr);
        lastLetter = first;
      }

      const row = document.createElement('div');
      row.className = 'song-row';
      if (inCurrent.has(Number(s.id))) row.classList.add('dim');

      const colTitle = document.createElement('div');
      colTitle.innerHTML = `<strong>${escapeHtml(s.title || '')}</strong><br><span class="badge">${escapeHtml(s.artist || '')}</span>`;

      const colKey = document.createElement('div');
      colKey.innerHTML = `<span class="badge">Key: ${escapeHtml(s.song_key || '-')}</span>`;

      const colBpm = document.createElement('div');
      colBpm.innerHTML = `<span class="badge">BPM: ${escapeHtml(String(s.bpm ?? '—'))}</span>`;

      const colAdd = document.createElement('div');
      const btn = document.createElement('button');
      btn.className = 'icon-btn';
      btn.textContent = inCurrent.has(Number(s.id)) ? 'Added ✓' : '+ Add';
      btn.disabled = inCurrent.has(Number(s.id));
      btn.addEventListener('click', () => addSongToCurrent(s.id));
      colAdd.appendChild(btn);

      row.appendChild(colTitle);
      row.appendChild(colKey);
      row.appendChild(colBpm);
      row.appendChild(colAdd);

      // also allow clicking row body to add
      row.addEventListener('click', (e) => {
        if (e.target === btn) return; // button already handles it
        if (!btn.disabled) btn.click();
      });

      pickerRows.appendChild(row);
    }
  }

  function renderCurrent() {
    setlistRows.innerHTML = '';
    countBadge.textContent = `(${CURRENT.length} song${CURRENT.length === 1 ? '' : 's'})`;
    // Join CURRENT with SONGS for display
    const byId = new Map(SONGS.map(s => [Number(s.id), s]));
    CURRENT.sort((a,b) => a.position - b.position);

    CURRENT.forEach((it, idx) => {
      const s = byId.get(Number(it.song_id)) || {};
      const row = document.createElement('div');
      row.className = 'set-row';

      const colTitle = document.createElement('div');
      colTitle.innerHTML = `<strong>${it.position}. ${escapeHtml(s.title || '')}</strong><br><span class="badge">${escapeHtml(s.artist || '')} • Key ${escapeHtml(s.song_key || '-') } • BPM ${escapeHtml(String(s.bpm ?? '—'))}</span>`;

      const colUp = document.createElement('div');
      const btnUp = document.createElement('button');
      btnUp.className = 'icon-btn';
      btnUp.textContent = '↑';
      btnUp.disabled = idx === 0;
      btnUp.addEventListener('click', () => moveItem(idx, -1));
      colUp.appendChild(btnUp);

      const colDown = document.createElement('div');
      const btnDown = document.createElement('button');
      btnDown.className = 'icon-btn';
      btnDown.textContent = '↓';
      btnDown.disabled = idx === CURRENT.length - 1;
      btnDown.addEventListener('click', () => moveItem(idx, +1));
      colDown.appendChild(btnDown);

      const colDel = document.createElement('div');
      const btnDel = document.createElement('button');
      btnDel.className = 'icon-btn';
      btnDel.textContent = 'Remove';
      btnDel.addEventListener('click', () => removeItem(idx));
      colDel.appendChild(btnDel);

      row.appendChild(colTitle);
      row.appendChild(colUp);
      row.appendChild(colDown);
      row.appendChild(colDel);

      setlistRows.appendChild(row);
    });

    // re-render picker to update Added ✓ state
    renderPicker();
  }

  function renumberPositions() {
    CURRENT.forEach((it, i) => it.position = i + 1);
  }

  // ---------- Actions ----------
  function addSongToCurrent(songId) {
    const sid = Number(songId);
    if (CURRENT.some(it => it.song_id === sid)) return;
    CURRENT.push({ song_id: sid, position: CURRENT.length + 1, set_number: SET_NUMBER });
    renderCurrent();
  }

  function removeItem(index) {
    CURRENT.splice(index, 1);
    renumberPositions();
    renderCurrent();
  }

  function moveItem(index, delta) {
    const newIdx = index + delta;
    if (newIdx < 0 || newIdx >= CURRENT.length) return;
    const [item] = CURRENT.splice(index, 1);
    CURRENT.splice(newIdx, 0, item);
    renumberPositions();
    renderCurrent();
  }

  async function saveSetlist() {
    if (!SETLIST_ID) {
      toastError('No setlist ID—create or load a setlist first.');
      return;
    }
    try {
      btnSave.disabled = true;
      const payload = CURRENT.map(it => ({ song_id: it.song_id, position: it.position, set_number: it.set_number }));
      await post('save_setlist_songs', { setlist_id: SETLIST_ID, items: JSON.stringify(payload) });
      toastNotice('Setlist saved ✓');
    } catch (e) {
      toastError('Save error: ' + e.message);
    } finally {
      btnSave.disabled = false;
    }
  }

  btnSave.addEventListener('click', saveSetlist);

  // ---------- Util ----------
  function toastNotice(msg) { notice.textContent = msg; notice.style.display = 'block'; errorBox.style.display = 'none'; }
  function toastError(msg)  { errorBox.textContent = msg; errorBox.style.display = 'block'; notice.style.display = 'none'; }
  function escapeHtml(s) { return (s||'').replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c])); }

  // ---------- Loaders ----------
  async function initSongs() {
    const data = await post('get_songs');
    SONGS = data.songs || [];
    renderPicker();
  }

  async function loadExistingSetlist(id) {
    const data = await post('get_setlist_detail', { setlist_id: id });
    spanName.textContent = data.name || '(unnamed)';
    spanId.textContent = id;
    headerWrap.style.display = 'block';
    CURRENT = (data.items || []).map((r, i) => ({
      song_id: Number(r.song_id),
      position: Number(r.position ?? (i+1)),
      set_number: Number(r.set_number ?? 1),
    }));
    builder.style.display = 'grid';
    statusText.textContent = 'Editing existing setlist.';
    await initSongs();
    renderCurrent();
  }

  // ---------- Create new setlist (modal submit) ----------
  formNew.addEventListener('submit', async (e) => {
    e.preventDefault();
    errorBox.style.display = 'none';
    notice.style.display = 'none';

    const name = inputName.value.trim();
    if (!name) { inputName.focus(); return; }

    formNew.querySelectorAll('button').forEach(b => b.disabled = true);

    try {
      const data = await post('create_setlist', { name });
      SETLIST_ID = Number(data.setlist_id);
      spanName.textContent = data.name;
      spanId.textContent = SETLIST_ID;
      headerWrap.style.display = 'block';
      statusText.textContent = 'Setlist created.';
      builder.style.display = 'grid';
      CURRENT = [];
      // Update URL
      const url = new URL(window.location.href);
      url.searchParams.set('setlist_id', String(SETLIST_ID));
      window.history.replaceState({}, '', url.toString());

      await initSongs();
      renderCurrent();
      closeModal();
      toastNotice('Saved ✓ You can add songs now.');
    } catch (err) {
      toastError('Error: ' + err.message);
    } finally {
      formNew.querySelectorAll('button').forEach(b => b.disabled = false);
    }
  });

  // ---------- Boot ----------
  const params = new URLSearchParams(window.location.search);
  const existingId = params.get('setlist_id');
  if (existingId) {
    SETLIST_ID = Number(existingId);
    loadExistingSetlist(SETLIST_ID).catch(e => toastError(e.message));
  } else {
    statusText.textContent = 'No setlist yet. Click “Create New Setlist.”';
  }
})();
</script>
</body>
</html>
