document.addEventListener('DOMContentLoaded', () => {
  const wires = document.querySelectorAll('[data-wire]');

  wires.forEach((wire) => {
    wire.addEventListener('click', () => {
      if (wire.classList.contains('cut')) {
        return;
      }

      const name = wire.dataset.wire;

      // WORK IN PROGRESS — substitua este alert pela lógica de fases.
      alert('[WIP] Fio ' + name + ' selecionado.');
    });
  });
});
