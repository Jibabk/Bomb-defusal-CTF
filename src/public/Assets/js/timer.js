document.addEventListener('DOMContentLoaded', () => {
  let totalSeconds = Number(document.body.dataset.remaining || 0);
  let isStarted = document.body.dataset.started === '1';
  let isDefused = document.body.dataset.defused === '1';
  let isExpired = document.body.dataset.expired === '1';
  let defusedAltInterval = null;

  const display = document.getElementById('timer-display');
  const defuseButton = document.getElementById('defuse-button');
  const input = document.getElementById('hex-input');
  const controls = document.querySelectorAll('[data-key], [data-clear]');
  const wireLinks = document.querySelectorAll('[data-wire-link]');
  let refreshInterval = null;

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


let altTick = false;
let altCode = null;

async function fetchDefuseCode() {
  try {
    const response = await fetch('index.php?route=defuse_code', {
      headers: { Accept: 'application/json' },
      cache: 'no-store',
    });
    if (!response.ok) return;
    const data = await response.json();
    altCode = data.code;
  } catch {
    return;
  }
}

async function initDefusedDisplay() {
  display.classList.add('safe');
  disableWireLinks();
  await fetchDefuseCode();
  defusedAltInterval = setInterval(() => {
    altTick = !altTick;
    display.textContent = altTick ? 'SAFE' : (altCode ?? '------');
  }, 1000);
  display.textContent = 'SAFE';
}

function renderTimer() {
  if (!isStarted) {
    display.textContent = '50:00';
    disableWireLinks();
    return;
  }

  if (isDefused) {
    if (!defusedAltInterval) initDefusedDisplay();
    return;
  }
  if (isExpired || totalSeconds <= 0) {
    display.textContent = '00:00';
    display.classList.add('panic');
    if (defuseButton) {
      defuseButton.classList.add('is-expired');
      defuseButton.disabled = true;
    }
    if (input) input.disabled = true;
    controls.forEach((control) => { control.disabled = true; });
    disableWireLinks();
    return;
  }
  const minutes = Math.floor(totalSeconds / 60);
  const seconds = totalSeconds % 60;
  display.textContent = pad(minutes) + ':' + pad(seconds);
  if (totalSeconds <= 30) display.classList.add('panic');
}

  async function refreshTimer() {
    try {
      const response = await fetch('index.php?route=timer', {
        headers: {
          Accept: 'application/json',
        },
        cache: 'no-store',
      });

      if (!response.ok) {
        return;
      }

      const timerStatus = await response.json();
      const wasDefused = isDefused;
      const wasExpired = isExpired;

      totalSeconds = Number(timerStatus.remainingSeconds || 0);
      isStarted = timerStatus.isStarted === true;
      isDefused = timerStatus.isDefused === true;
      isExpired = timerStatus.isExpired === true;

      if (!wasDefused && isDefused) {
        window.BombAudio?.stopBeep();
        window.BombAudio?.play('defuse');
      } else if (!wasExpired && isExpired) {
        window.BombAudio?.stopBeep();
        window.BombAudio?.play('explosion');
      } else if (isStarted && !isDefused && !isExpired) {
        window.BombAudio?.startBeep();
      }
      
      altTick = !altTick;
      renderTimer();

      if ((!isStarted || isDefused || isExpired) && refreshInterval !== null) {
        clearInterval(refreshInterval);
        refreshInterval = null;
      }
    } catch (error) {
      return;
    }
  }

  renderTimer();
  if (isStarted && !isDefused && !isExpired) {
    refreshTimer();
    refreshInterval = setInterval(refreshTimer, 1000);
  }
});
