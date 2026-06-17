document.addEventListener('DOMContentLoaded', () => {
  let totalSeconds = Number(document.body.dataset.remaining || 0);
  const isDefused = document.body.dataset.defused === '1';
  const startsExpired = document.body.dataset.expired === '1';

  const display = document.getElementById('timer-display');
  const defuseButton = document.getElementById('defuse-button');
  const input = document.getElementById('hex-input');
  const controls = document.querySelectorAll('[data-key], [data-clear]');
  const wireLinks = document.querySelectorAll('[data-wire-link]');

  if (!display) {
    return;
  }

  function disableWireLinks() {
    wireLinks.forEach((wire) => {
      wire.removeAttribute('href');
      wire.removeAttribute('target');
      wire.removeAttribute('rel');
      wire.classList.add('is-disabled');
      wire.setAttribute('aria-disabled', 'true');
    });
  }

  function pad(n) {
    return String(n).padStart(2, '0');
  }

  function renderTimer() {
    if (isDefused) {
      display.textContent = 'SAFE';
      display.classList.add('safe');
      disableWireLinks();

      return;
    }

    if (totalSeconds <= 0) {
      display.textContent = '00:00';
      display.classList.add('panic');

      if (defuseButton) {
        defuseButton.classList.add('is-expired');
        defuseButton.disabled = true;
      }

      if (input) {
        input.disabled = true;
      }

      controls.forEach((control) => {
        control.disabled = true;
      });

      disableWireLinks();

      return;
    }

    const minutes = Math.floor(totalSeconds / 60);
    const seconds = totalSeconds % 60;

    display.textContent = pad(minutes) + ':' + pad(seconds);

    if (totalSeconds <= 30) {
      display.classList.add('panic');
    }
  }

  function tick() {
    if (totalSeconds > 0) {
      totalSeconds--;
    }

    renderTimer();
  }

  renderTimer();
  if (!isDefused && !startsExpired) {
    setInterval(tick, 1000);
  }
});
