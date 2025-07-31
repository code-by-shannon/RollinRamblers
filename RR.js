document.addEventListener("DOMContentLoaded", () => {
  const flipImage = (img) => {
    const front = img.dataset.front;
    const back = img.dataset.back;

    if (!front || !back) return; // safety check

    img.src = img.src.includes(front) 
      ? `imgs/${back}`
      : `imgs/${front}`;
  };

  document.querySelectorAll('.gallery-image').forEach((img) => {
    const front = img.dataset.front?.trim();
    const back = img.dataset.back?.trim();

    if (front && back) {
      img.addEventListener('click', () => flipImage(img));
    }
  });
});











  







