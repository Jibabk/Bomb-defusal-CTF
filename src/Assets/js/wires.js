document.addEventListener('DOMContentLoaded', () => {
  const form = document.getElementById('defuse-form');
  const input = document.getElementById('hex-input');
  const preview = document.getElementById('entry-preview');
  const keys = document.querySelectorAll('[data-key]');
  const clearButton = document.querySelector('[data-clear]');
  const defuseButton = document.getElementById('defuse-button');

  if (!form || !input) {
    return;
  }

  function normalize(value) {
    return value.toUpperCase().replace(/[^0-9A-F]/g, '').slice(0, 6);
  }

  function renderPreview() {
    if (!preview) {
      return;
    }

    const cursor = input.disabled || input.value.length >= 6 ? '' : '_';
    preview.textContent = (input.value + cursor).padEnd(6, ' ');
  }

  input.addEventListener('keydown', (event) => {
    event.preventDefault();
  });

  input.addEventListener('paste', (event) => {
    event.preventDefault();
  });

  function bindButtonSound(button) {
    if (!button) {
      return;
    }

    button.addEventListener('pointerdown', () => {
      window.BombAudio?.buttonPress();
    });
  }

  keys.forEach((key) => {
    bindButtonSound(key);

    key.addEventListener('click', () => {
      if (input.disabled || input.value.length >= 6) {
        return;
      }

      input.value = normalize(input.value + key.dataset.key);
      input.classList.remove('is-invalid');
      input.focus();
      renderPreview();
    });
  });

  if (clearButton) {
    bindButtonSound(clearButton);

    clearButton.addEventListener('click', () => {
      if (input.disabled) {
        return;
      }

      input.value = '';
      input.classList.remove('is-invalid');
      input.focus();
      renderPreview();
    });
  }

  bindButtonSound(defuseButton);

  form.addEventListener('submit', () => {
    input.value = normalize(input.value);
  });

  renderPreview();
});
