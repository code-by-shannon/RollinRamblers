const songTitle = document.getElementById('song-title');
const artist = document.getElementById('artist');


document.addEventListener("DOMContentLoaded", () => {
    const table = document.getElementById("myTable");
    let sortDirection = 1;
  
    function sortTableByColumn(columnIndex) {
      const tbody = table.tBodies[0];
      const rows = Array.from(tbody.querySelectorAll("tr"));
  
      rows.sort((a, b) => {
        const aText = a.cells[columnIndex].textContent.trim().toLowerCase();
        const bText = b.cells[columnIndex].textContent.trim().toLowerCase();
  
        if (aText < bText) return -1 * sortDirection;
        if (aText > bText) return 1 * sortDirection;
        return 0;
      });
  
      // Flip direction for next click
      sortDirection *= -1;
  
      // Reattach sorted rows
      rows.forEach(row => tbody.appendChild(row));
    }
  
    document.getElementById("song-title").addEventListener("click", () => {
      sortTableByColumn(0);
    });
  
    document.getElementById("artist").addEventListener("click", () => {
      sortTableByColumn(1);
    });
  });
  