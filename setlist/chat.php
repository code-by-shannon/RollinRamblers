<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Setlist Builder</title>
  <style>
    body { font-family: sans-serif; padding: 1rem; max-width: 600px; margin: auto; }
    .setlist { margin-top: 1rem; border: 1px solid #ccc; padding: 1rem; border-radius: 5px; }
    .song { display: flex; justify-content: space-between; margin-bottom: 0.5rem; background: #f4f4f4; padding: 0.5rem; border-radius: 3px; cursor: move; }
    .remove { color: red; cursor: pointer; }
    .controls { margin-top: 1rem; text-align: center; }
  </style>
</head>
<body>
  <h1>Create a Setlist</h1>

  <label for="setlistName">Setlist Name:</label><br>
  <input type="text" id="setlistName" placeholder="e.g. Frank's Tavern">

  <h2>Add Songs</h2>
  <input type="text" id="songInput" placeholder="Song name">
  <input type="text" id="artistInput" placeholder="Artist">
  <button onclick="addSong()">Add Song</button>

  <div class="setlist" id="setlistContainer">
    <!-- Songs will appear here -->
  </div>

  <div class="controls">
    <button onclick="saveSetlist()">💾 Save</button>
    <button onclick="loadSetlist()">📂 Load</button>
  </div>

  <script>
    let setlist = [];

    function renderSetlist() {
      const container = document.getElementById("setlistContainer");
      container.innerHTML = "";
      setlist.forEach((song, index) => {
        const div = document.createElement("div");
        div.className = "song";
        div.draggable = true;
        div.innerHTML = `<span>${song.song} - ${song.artist}</span><span class="remove" onclick="removeSong(${index})">❌</span>`;
        div.ondragstart = e => e.dataTransfer.setData("text/plain", index);
        div.ondragover = e => e.preventDefault();
        div.ondrop = e => {
          const from = e.dataTransfer.getData("text/plain");
          const to = index;
          [setlist[from], setlist[to]] = [setlist[to], setlist[from]];
          renderSetlist();
        };
        container.appendChild(div);
      });
    }

    function addSong() {
      const song = document.getElementById("songInput").value.trim();
      const artist = document.getElementById("artistInput").value.trim();
      if (!song || !artist) return;
      setlist.push({ song, artist });
      document.getElementById("songInput").value = "";
      document.getElementById("artistInput").value = "";
      renderSetlist();
    }

    function removeSong(index) {
      setlist.splice(index, 1);
      renderSetlist();
    }

    function saveSetlist() {
      const name = document.getElementById("setlistName").value.trim();
      if (!name) return alert("Please name your setlist.");
      const data = { name, songs: setlist };
      localStorage.setItem("setlist", JSON.stringify(data));
      alert("Setlist saved.");
    }

    function loadSetlist() {
      const data = localStorage.getItem("setlist");
      if (!data) return alert("No setlist found.");
      const { name, songs } = JSON.parse(data);
      document.getElementById("setlistName").value = name;
      setlist = songs;
      renderSetlist();
    }
  </script>
</body>
</html>
