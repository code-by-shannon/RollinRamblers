<?php
// Basic setup
include 'includes/db.php'; // or your db connect logic
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Create Setlist</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <style>
    body { 
      font-family: sans-serif; padding: 1rem; 
    }
    #main-div{
      margin:auto;
      color:green;
      border:dashed red 1px;
      width:50%;
      text-align: center;
    }
    .set-controls { 
      margin-bottom: 1rem;
    }
    .song-result, .selected-song {
      display: flex;
      justify-content: space-between;
      margin: 0.2rem 0;
      padding: 0.5rem;
      border: 1px solid #ccc;
      border-radius: 4px;
      background: #f8f8f8;
    }
    .selected-song { 
      cursor: grab; 
    }
    .remove { 
      color: red; cursor: pointer; 
    }
    .sticky-save {
      position: fixed;
      bottom: 1rem;
      left: 0; right: 0;
      padding: 1rem;
      background: #000;
      color: #fff;
      text-align: center;
    }
    #results{
      width:40%;
    }
  </style>
</head>
<body>
<div id="main-div">
<h1>Create Setlist</h1>

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

  <script>
    let activeSet = 1;
    const sets = { 1: [] };

    function addSet() {
      activeSet++;
      sets[activeSet] = [];
      document.getElementById("active-set-label").textContent = `Set ${activeSet}`;
      renderSelected();
    }

    function searchSongs() {
      const query = document.getElementById('songSearch').value;
      if (query.length < 2) return document.getElementById('results').innerHTML = '';
      
      fetch(`search_songs.php?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
          const html = data.map(song => `
  <div class="song-result">
    <span>${song.song} – ${song.artist}</span>
    <button 
  class="add-button" 
  data-id="${song.id}" 
  data-name="${encodeURIComponent(song.song)}" 
  data-artist="${encodeURIComponent(song.artist)}"
>➕</button>

  </div>`).join('');

          document.getElementById('results').innerHTML = html;
        });
    }

    function addSong(id, name, artist) {
      sets[activeSet].push({ id, name, artist });
      renderSelected();
    }

    function removeSong(index) {
      sets[activeSet].splice(index, 1);
      renderSelected();
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
      fetch('save_setlist.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(sets)
      })
      .then(res => res.text())
      .then(msg => alert(msg))
      .catch(err => alert("Failed to save setlist."));
    }

    document.getElementById('results').addEventListener('click', function(e) {
  if (e.target.classList.contains('add-button')) {
    const id = parseInt(e.target.dataset.id);
    const name = decodeURIComponent(e.target.dataset.name);
    const artist = decodeURIComponent(e.target.dataset.artist);
    addSong(id, name, artist);
  }
});

  </script>


</div>

<hr>

<h3>Master List</h3>
<div style="white-space: pre-line; font-family: monospace;">
  <?php include 'song_master_list.php'; ?>
</div>

</body>
</html>
