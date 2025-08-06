let audioCtx = new (window.AudioContext || window.webkitAudioContext)();
let bpm = 120;
let interval = 60 / bpm;
let nextNoteTime = 0;
let isPlaying = false;
let timerID;
let lookahead = 25; // ms
let scheduleAheadTime = 0.1; // seconds

function scheduleNote(time) {
  const osc = audioCtx.createOscillator();
  const gain = audioCtx.createGain();

  osc.frequency.value = 1000;
  gain.gain.setValueAtTime(1, time);
  gain.gain.exponentialRampToValueAtTime(0.001, time + 0.05);

  osc.connect(gain);
  gain.connect(audioCtx.destination);
  osc.start(time);
  osc.stop(time + 0.05);
}

function scheduler() {
  while (nextNoteTime < audioCtx.currentTime + scheduleAheadTime) {
    scheduleNote(nextNoteTime);
    nextNoteTime += interval;
  }
  timerID = setTimeout(scheduler, lookahead);
}

function startMetronome(newBpm) {
  if (isPlaying) return;
  bpm = newBpm;
  interval = 60 / bpm;
  nextNoteTime = audioCtx.currentTime + 0.05;
  isPlaying = true;
  scheduler();
  // document.getElementById('metronome-visual').style.opacity = 1;
  pulse();
}

function stopMetronome() {
  isPlaying = false;
  clearTimeout(timerID);
  // document.getElementById('metronome-visual').style.opacity = 0;
}

function pulse() {
  if (!isPlaying) return;
  // Skip visual stuff entirely
  setTimeout(() => {
    if (isPlaying) setTimeout(pulse, interval * 1000);
  }, 100);
}


let currentBpm = null;

document.addEventListener('click', function (e) {
  if (e.target.classList.contains('bpm-cell')) {
    const clickedBpm = parseInt(e.target.dataset.bpm);

    if (isPlaying && currentBpm === clickedBpm) {
      stopMetronome();
      currentBpm = null;
    } else {
      stopMetronome(); // stop any currently running one
      audioCtx.resume().then(() => {
        startMetronome(clickedBpm);
        currentBpm = clickedBpm;
      });
    }
  }
});

