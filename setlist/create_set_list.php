<?php
// Basic setup
include 'includes/db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Setlist</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { font-family: sans-serif; padding: 1rem; }
    #main-div { margin: auto; width: 50%; text-align: center; }
    .set-controls { margin-bottom: 1rem; }
    .song-result, .selected-song {
      display: flex; justify-content: space-between;
      margin: 0.2rem 0; padding: 0.5rem;
      border: 1px solid #ccc; border-radius: 4px; background: #f8f8f8;
    }
    .selected-song { cursor: grab; }
    .remove { color: red; cursor: pointer; }
    .sticky-save {
      position: fixed; bottom: 1rem; left: 0; right: 0;
      padding: 1rem; background: #000; color: #fff; text-align: center;
    }
    #results { width: 40%; margin: auto; }
  </style>
</head>
<body>
<div id="main-div">

  <!-- Step 1: Setup UI -->
  <div id="setupUI">
    <button onclick="loadSetlist()">📂 Load Previously Saved Setlist</button>
    <button onclick="promptNewSetlist()">🆕 Create New Setlist</button>

    <div id="newSetName" style="margin-top:1rem; display:none;">
      <label for="setlistNameInput">Enter setlist name:</label><br>
      <input type="text" id="setlistNameInput" placeholder="e.g. Frank's Tavern">
    </div>
  </div>

  <!-- Step 2: Builder UI -->
  <div id="builderUI" style="display: none;">
    <h1 id="setlistTitle">Create Setlist</h1>

    <div class="set-controls">
      <button onclick="addSet()">➕ Add Set</button>
      <span id="active-set-label">Set 1</span>
    </div>

    <label for="songSearch">Start typing a song title:</label><br>
    <input type="text" id="songSearch" placeholder="Search songs..." onkeyup="searchSongs()" autocomplete="off">
    <div id="results"></div>

    <h2>Selected Songs</h2>
    <div id="selectedSongs"></div>

    <div class="sticky-save">
      <button onclick="submitSetlist()">💾 Save Setlist</button>
    </div>
  </div>
</div>

<hr>
<h3>Master List</h3>
<div style="white-space: pre-line; font-family: monospace;">
  <?php include 'song_master_list.php'; ?>
</div>

<script>
let activeSet = 1;
let sets = { 1: [] };
let setlistName = '';

const savedSetNum = localStorage.getItem('activeSet');
const savedSets = localStorage.getItem('savedSets');
if (savedSets) {
  sets = JSON.parse(savedSets);
  renderSelected();
}
if (savedSetNum) activeSet = parseInt(savedSetNum);

function loadSetlist() {
  alert("🔜 Load feature coming soon.");
}

function promptNewSetlist() {
  document.getElementById("newSetName").style.display = "block";
  document.getElementById("setlistNameInput").focus();
}

document.getElementById("setlistNameInput").addEventListener("keydown", function(e) {
  if (e.key === "Enter") {
    const name = this.value.trim();
    if (name === '') return;
    setlistName = name;
    localStorage.setItem('setlistName', setlistName);
    document.getElementById("setlistTitle").textContent = `🎸 Setlist: ${setlistName}`;
    document.getElementById("setupUI").style.display = "none";
    document.getElementById("builderUI").style.display = "block";
  }
});

function addSet() {
  activeSet++;
  sets[activeSet] = [];
  document.getElementById("active-set-label").textContent = `Set ${activeSet}`;
  renderSelected();
}

function searchSongs() {
  const query = document.getElementById('songSearch').value;
  if (query.length < 1) return document.getElementById('results').innerHTML = '';

  fetch(`search_songs.php?q=${encodeURIComponent(query)}`)
    .then(res => res.json())
    .then(data => {
      const html = data.map(song => `
        <div class="song-result">
          <span>${song.song} – ${song.artist}</span>
          <button class="add-button" data-id="${song.id}" data-name="${encodeURIComponent(song.song)}" data-artist="${encodeURIComponent(song.artist)}">➕</button>
        </div>`).join('');
      document.getElementById('results').innerHTML = html;
    });
}

document.getElementById('results').addEventListener('click', function(e) {
  if (e.target.classList.contains('add-button')) {
    const id = parseInt(e.target.dataset.id);
    const name = decodeURIComponent(e.target.dataset.name);
    const artist = decodeURIComponent(e.target.dataset.artist);
    addSong(id, name, artist);
  }
});

function addSong(id, name, artist) {
  sets[activeSet].push({ id, name, artist });
  document.getElementById('songSearch').value = '';
  document.getElementById('results').innerHTML = '';
  renderSelected();
  localStorage.setItem('savedSets', JSON.stringify(sets));
}

function removeSong(index) {
  sets[activeSet].splice(index, 1);
  renderSelected();
  localStorage.setItem('savedSets', JSON.stringify(sets));
}

function renderSelected() {
  const container = document.getElementById('selectedSongs');
  container.innerHTML = sets[activeSet].map((s, i) => `
    <div class="selected-song">
      <span>${s.name} – ${s.artist}</span>
      <span class="remove" onclick="removeSong(${i})">❌</span>
    </div>`).join('');
}

function submitSetlist() {
  if (!setlistName || sets[activeSet].length === 0) {
    alert("⚠️ Setlist must have a name and at least one song.");
    return;
  }

  fetch('save_setlist.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ name: setlistName, sets: sets })
  })
  .then(res => res.text())
  .then(msg => alert(msg))
  .catch(err => alert("❌ Failed to save setlist."));
}
</script>

</body>
</html>
