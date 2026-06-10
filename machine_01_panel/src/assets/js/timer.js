document.addEventListener('DOMContentLoaded', () => {
  let totalSeconds = Number(document.body.dataset.remaining || 0);

  const display = document.getElementById('timer-display');
  const status = document.getElementById('timer-status');
  const armedLed = document.getElementById('armed-led');

  function pad(n) {
    return String(n).padStart(2, '0');
  }

  function renderTimer() {
    if (totalSeconds <= 0) {
      display.textContent = '00:00';
      display.classList.add('panic');
      status.textContent = '■ DETONATION IMMINENT';
      return;
    }

    const minutes = Math.floor(totalSeconds / 60);
    const seconds = totalSeconds % 60;

    display.textContent = pad(minutes) + ':' + pad(seconds);

    if (totalSeconds <= 30) {
      display.classList.add('panic');
      status.textContent = '■ CRITICAL — ' + totalSeconds + 's REMAINING';
      armedLed.style.animationDuration = '.3s';
    }
  }

  function tick() {
    if (totalSeconds > 0) {
      totalSeconds--;
    }

    renderTimer();
  }

  renderTimer();
  setInterval(tick, 1000);
});
