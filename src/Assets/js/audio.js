(() => {
  const audioPaths = {
    beep: 'Assets/audio/beep.wav',
    buttonPress: 'Assets/audio/button_press.wav',
    defuse: 'Assets/audio/defuse.wav',
    error: 'Assets/audio/error.wav',
    explosion: 'Assets/audio/explosion.wav',
    success: 'Assets/audio/success.wav',
  };

  const sounds = {};
  let pendingEvent = '';

  function getSound(name) {
    if (!sounds[name]) {
      sounds[name] = new Audio(audioPaths[name]);
      sounds[name].preload = 'auto';
    }

    return sounds[name];
  }

  function play(name, restart = true) {
    if (!audioPaths[name]) {
      return;
    }

    const sound = getSound(name);

    if (restart) {
      sound.currentTime = 0;
    }

    const promise = sound.play();

    if (promise && typeof promise.catch === 'function') {
      promise.catch(() => {
        if (name !== 'beep') {
          pendingEvent = name;
        }
      });
    }
  }

  function startBeep() {
    const beep = getSound('beep');
    beep.loop = true;

    if (beep.paused) {
      play('beep', false);
    }
  }

  function stopBeep() {
    const beep = getSound('beep');
    beep.pause();
    beep.currentTime = 0;
  }

  function unlockAudio() {
    if (pendingEvent) {
      const event = pendingEvent;
      pendingEvent = '';
      play(event);
    }

    if (
      document.body.dataset.started === '1'
      && document.body.dataset.defused !== '1'
      && document.body.dataset.expired !== '1'
    ) {
      startBeep();
    }
  }

  window.BombAudio = {
    play,
    startBeep,
    stopBeep,
    buttonPress() {
      play('buttonPress');
    },
  };

  document.addEventListener('DOMContentLoaded', () => {
    const audioEvent = document.body.dataset.audioEvent || '';

    if (audioEvent) {
      play(audioEvent);
    }

    if (
      document.body.dataset.started === '1'
      && document.body.dataset.defused !== '1'
      && document.body.dataset.expired !== '1'
    ) {
      startBeep();
    }

    document.addEventListener('pointerdown', unlockAudio);
    document.addEventListener('keydown', unlockAudio);
  });
})();
