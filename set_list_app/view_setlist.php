<?php require __DIR__ . '/config.php'; 
include 'header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>View Setlist (Read-Only)</title>

<script>
  window.__CONFIG__ = {
    BASE_URL: "<?php echo rtrim($config['base_url'], '/'); ?>",
    API_PREFIX: "<?php echo rtrim($config['api_prefix'], '/'); ?>"
  };
</script>

<style>
  :root { color-scheme: dark; }
  body { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; margin:0; padding:24px; background:#0b0b0b; color:#eaeaea; }
  h1 { margin-top:0; }
  .row { display:flex; gap:12px; align-items:center; flex-wrap:wrap; }
  select, button { background:#141414; color:#eaeaea; border:1px solid #2a2a2a; border-radius:8px; padding:8px 10px; }
  .card { background:#111; border:1px solid #222; border-radius:12px; padding:16px; margin-top:16px; }
  .muted { color:#a8a8a8; }
  .table { width:100%; border-collapse: collapse; margin-top:12px; }
  .table th, .table td { border-bottom:1px solid #222; padding:10px; text-align:left; }
  .table th { background:#141414; }
  .label { display:inline-block; font-size:12px; padding:2px 8px; border-radius:999px; border:1px solid #2a2a2a; color:#bdbdbd; }
  .set-header { margin-top:16px; font-weight:700; }
  .error { color:#ff8e8e; margin-top:10px; display:none; white-space:pre-wrap; }
</style>
</head>
<body>
  <h1>View Setlist (Read-Only)</h1>

  <div class="row">
    <label for="selSetlist">Select setlist:</label>
    <select id="selSetlist">
      <option value="">— choose —</option>
    </select>
    <span id="meta" class="muted"></span>
  </div>
  <div id="err" class="error"></div>

  <div id="output" class="card" style="display:none;">
    <div id="titleLine" class="row" style="justify-content:space-between;">
      <div><strong id="setlistName"></strong></div>
      <div class="muted" id="setlistDate"></div>
    </div>
    <div id="listContainer"></div>
  </div>

<script>
(async function () {
  const sel = document.getElementById('selSetlist');
  const meta = document.getElementById('meta');
  const out = document.getElementById('output');
  const setlistName = document.getElementById('setlistName');
  const setlistDate = document.getElementById('setlistDate');
  const listContainer = document.getElementById('listContainer');
  const err = document.getElementById('err');

  const BASE = (window.__CONFIG__.BASE_URL + window.__CONFIG__.API_PREFIX).replace(/\/+$/,'');
  const api = async (path, body) => {
    const url = BASE + path;
    console.log('API →', url);
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: new URLSearchParams(body || {})
    });
    const text = await res.text();
    // Enforce JSON, surface HTML (e.g., 404 page) clearly:
    try { return JSON.parse(text); }
    catch { throw new Error(`Non-JSON response (${res.status}) from ${url}:\n` + text.slice(0,300)); }
  };

  const escapeHtml = (s)=> (s||'').toString().replace(/[&<>"']/g, c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));

  function groupBySet(items) {
    const map = new Map();
    for (const it of items) {
      const sn = Number(it.set_number ?? 1);
      if (!map.has(sn)) map.set(sn, []);
      map.get(sn).push(it);
    }
    for (const arr of map.values()) arr.sort((a,b)=>Number(a.position)-Number(b.position));
    return Array.from(map.entries()).sort((a,b)=>a[0]-b[0]);
  }

  function renderReadOnly(items) {
    listContainer.innerHTML = '';
    const sets = groupBySet(items);
    for (const [setNum, arr] of sets) {
      const header = document.createElement('div');
      header.className = 'set-header';
      header.textContent = `Set ${setNum}`;
      listContainer.appendChild(header);

      const table = document.createElement('table');
      table.className = 'table';
      table.innerHTML = `
        <thead>
          <tr>
            <th style="width:60px;">#</th>
            <th>Title</th>
            <th>Artist</th>
            <th style="width:90px;">Key</th>
            <th style="width:90px;">BPM</th>
          </tr>
        </thead>
        <tbody></tbody>`;
      const tbody = table.querySelector('tbody');

      arr.forEach((r) => {
        const tr = document.createElement('tr');
        tr.innerHTML = `
          <td>${escapeHtml(String(r.position))}</td>
          <td><strong>${escapeHtml(r.title)}</strong></td>
          <td>${escapeHtml(r.artist || '')}</td>
          <td><span class="label">${escapeHtml(r.song_key || '-')}</span></td>
          <td>${escapeHtml(String(r.bpm ?? '—'))}</td>`;
        tbody.appendChild(tr);
      });
      listContainer.appendChild(table);
    }
  }

  async function loadSetlists() {
    try {
      const data = await api('/list_setlists.php');
      if (!data.ok) throw new Error(data.error || 'Unknown error');
      for (const s of data.setlists || []) {
        const opt = document.createElement('option');
        opt.value = s.id;
        opt.textContent = `${s.name} (${s.created_at})`;
        sel.appendChild(opt);
      }
      meta.textContent = `${(data.setlists||[]).length} saved setlist(s)`;
    } catch (e) {
      err.textContent = 'Error loading setlists: ' + e.message;
      err.style.display = 'block';
    }
  }

  async function loadOne(id) {
    try {
      err.style.display = 'none';
      const data = await api('/get_setlist_detail.php', { setlist_id: id });
      if (!data.ok) throw new Error(data.error || 'Unknown error');
      setlistName.textContent = data.meta?.name || '(unnamed)';
      setlistDate.textContent = data.meta?.created_at ? `Created: ${data.meta.created_at}` : '';
      renderReadOnly(data.items || []);
      out.style.display = 'block';
    } catch (e) {
      err.textContent = 'Error loading setlist: ' + e.message;
      err.style.display = 'block';
    }
  }

  sel.addEventListener('change', () => {
    const id = Number(sel.value || 0);
    if (!id) { out.style.display = 'none'; return; }
    loadOne(id);
  });

  await loadSetlists();

  // Deep link ?setlist_id=123
  const params = new URLSearchParams(window.location.search);
  const existingId = Number(params.get('setlist_id') || 0);
  if (existingId) {
    sel.value = String(existingId);
    if (sel.value) loadOne(existingId);
  }
})();
</script>
</body>
</html>
