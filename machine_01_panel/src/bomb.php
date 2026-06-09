<?php
session_start();

// Registra o instante de início na primeira visita desta sessão
if (!isset($_SESSION['bomb_start'])) {
    $_SESSION['bomb_start'] = time();
}

const BOMB_DURATION = 900; // 15 minutos em segundos

// Calcula quanto tempo resta (nunca negativo)
$elapsed  = time() - $_SESSION['bomb_start'];
$remaining = max(0, BOMB_DURATION - $elapsed);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BOMB DEFUSAL // UNIT MK-VII</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://fonts.googleapis.com/css2?family=VT323&family=Share+Tech+Mono&display=swap" rel="stylesheet">
  <style>
    :root {
      --steel-light: #c8cdd4;
      --steel-mid:   #8e9199;
      --steel-dark:  #3a3d42;
      --steel-edge:  #1e2024;
      --rivet:       #5a5f68;
      --lcd-bg:      #0a1a0a;
      --lcd-glow:    #00ff41;
      --lcd-dim:     #003010;
      --wire-sheath: 3px;
    }

    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      background-color: #1a1c1f;
      background-image:
        repeating-linear-gradient(0deg,   transparent, transparent 39px, rgba(0,0,0,.15) 39px, rgba(0,0,0,.15) 40px),
        repeating-linear-gradient(90deg,  transparent, transparent 39px, rgba(0,0,0,.15) 39px, rgba(0,0,0,.15) 40px);
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: 'Share Tech Mono', monospace;
      padding: 2rem 1rem;
    }

    /* ── Carcaça principal ── */
    .bomb-chassis {
      position: relative;
      width: 100%;
      max-width: 780px;
      background:
        linear-gradient(160deg, #b0b5bc 0%, #7a7f88 18%, #515660 45%, #3a3d44 55%, #52575f 78%, #9aa0a8 100%);
      border-radius: 6px;
      border: 2px solid var(--steel-edge);
      box-shadow:
        inset 0 1px 0 rgba(255,255,255,.25),
        inset 0 -1px 0 rgba(0,0,0,.6),
        0 8px 40px rgba(0,0,0,.8),
        0 2px 4px rgba(0,0,0,.9);
      padding: 28px 28px 32px;
    }

    /* Textura de escovado */
    .bomb-chassis::before {
      content: '';
      position: absolute;
      inset: 0;
      border-radius: 6px;
      background-image: repeating-linear-gradient(
        90deg,
        transparent,
        transparent 2px,
        rgba(255,255,255,.018) 2px,
        rgba(255,255,255,.018) 3px
      );
      pointer-events: none;
    }

    /* ── Parafusos de canto ── */
    .screw {
      position: absolute;
      width: 22px;
      height: 22px;
    }
    .screw svg { width: 100%; height: 100%; }
    .screw-tl { top: 10px;  left: 10px; }
    .screw-tr { top: 10px;  right: 10px; }
    .screw-bl { bottom: 10px; left: 10px; }
    .screw-br { bottom: 10px; right: 10px; }

    /* ── Plaqueta de identificação ── */
    .id-plate {
      background: linear-gradient(135deg, #c8a84b 0%, #e8c96a 30%, #b8982e 60%, #d4b44a 100%);
      border: 1px solid #7a6218;
      border-radius: 3px;
      padding: 5px 14px;
      display: inline-block;
      box-shadow: inset 0 1px 0 rgba(255,255,255,.3), 0 2px 4px rgba(0,0,0,.5);
      font-family: 'Share Tech Mono', monospace;
      font-size: 10px;
      letter-spacing: 2px;
      color: #2a1f08;
      text-transform: uppercase;
      margin-bottom: 6px;
      line-height: 1.6;
    }
    .id-plate .serial {
      font-size: 8px;
      letter-spacing: 1px;
      opacity: .7;
      display: block;
    }

    /* ── Seção superior: timer + plaqueta ── */
    .top-section {
      display: flex;
      align-items: flex-start;
      justify-content: space-between;
      margin-bottom: 22px;
      gap: 16px;
    }

    /* ── Display LCD ── */
    .lcd-housing {
      background: linear-gradient(145deg, #1e2124, #2a2d32, #16181a);
      border: 2px solid #0d0f10;
      border-radius: 4px;
      padding: 10px 14px 14px;
      box-shadow:
        inset 0 2px 8px rgba(0,0,0,.9),
        inset 0 0 2px rgba(0,0,0,.6),
        0 1px 0 rgba(255,255,255,.1);
      min-width: 260px;
    }
    .lcd-label {
      font-size: 9px;
      letter-spacing: 3px;
      color: #4a5060;
      text-transform: uppercase;
      margin-bottom: 6px;
    }
    .lcd-screen {
      background-color: var(--lcd-bg);
      border: 1px solid #001800;
      border-radius: 2px;
      padding: 10px 18px;
      box-shadow: inset 0 0 12px rgba(0,0,0,.8);
      position: relative;
      overflow: hidden;
    }
    .lcd-screen::before {
      content: '';
      position: absolute;
      inset: 0;
      background: repeating-linear-gradient(
        0deg,
        transparent,
        transparent 3px,
        rgba(0,0,0,.08) 3px,
        rgba(0,0,0,.08) 4px
      );
      pointer-events: none;
    }
    .lcd-digits {
      font-family: 'VT323', monospace;
      font-size: 58px;
      color: var(--lcd-glow);
      text-shadow: 0 0 8px rgba(0,255,65,.6), 0 0 20px rgba(0,255,65,.2);
      letter-spacing: 4px;
      line-height: 1;
      text-align: center;
    }
    .lcd-digits.panic {
      color: #ff2200;
      text-shadow: 0 0 8px rgba(255,34,0,.7), 0 0 20px rgba(255,34,0,.3);
      animation: lcd-pulse .4s ease-in-out infinite alternate;
    }
    @keyframes lcd-pulse { from { opacity: 1; } to { opacity: .6; } }

    .lcd-status {
      font-size: 10px;
      letter-spacing: 2px;
      color: var(--lcd-dim);
      text-align: center;
      margin-top: 6px;
      text-shadow: 0 0 4px rgba(0,255,65,.3);
    }
    .lcd-status.active { color: var(--lcd-glow); }

    /* ── Painel de controle ── */
    .panel-label {
      font-size: 9px;
      letter-spacing: 3px;
      color: #3a3f48;
      text-transform: uppercase;
      margin-bottom: 10px;
      padding-left: 4px;
    }

    /* ── Trilho de fios ── */
    .wire-panel {
      background: linear-gradient(180deg, #1a1c1f 0%, #141618 100%);
      border: 1px solid #0d0e10;
      border-radius: 4px;
      padding: 20px 16px;
      box-shadow: inset 0 2px 10px rgba(0,0,0,.9);
    }

    .wire-row {
      display: flex;
      flex-direction: column;
      gap: 14px;
    }

    /* ── Fio individual ── */
    .wire-item {
      display: flex;
      align-items: center;
      gap: 0;
      cursor: pointer;
      position: relative;
    }

    /* Terminal de entrada (PCB) */
    .wire-terminal-left,
    .wire-terminal-right {
      width: 18px;
      height: 28px;
      background: linear-gradient(180deg, #787c82 0%, #4a4e54 40%, #3a3d42 100%);
      border-radius: 2px;
      border: 1px solid #222428;
      box-shadow: inset 0 1px 0 rgba(255,255,255,.15), 0 1px 3px rgba(0,0,0,.5);
      flex-shrink: 0;
      position: relative;
    }
    .wire-terminal-left::after,
    .wire-terminal-right::after {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      width: 7px;
      height: 7px;
      border-radius: 50%;
      background: radial-gradient(circle at 35% 35%, #aab0b8, #555a62);
      border: 1px solid #1a1c1f;
    }

    /* Cabo do fio */
    .wire-cable {
      flex: 1;
      height: 14px;
      position: relative;
      display: flex;
      align-items: center;
    }

    .wire-cable-body {
      width: 100%;
      height: 12px;
      border-radius: 6px;
      position: relative;
      box-shadow:
        inset 0 3px 0 rgba(255,255,255,.18),
        inset 0 -3px 0 rgba(0,0,0,.45),
        0 2px 6px rgba(0,0,0,.6);
      transition: filter .15s, transform .1s;
    }

    /* Cores dos fios */
    .wire-red    .wire-cable-body { background: linear-gradient(180deg, #e83030 0%, #c01818 35%, #8a0808 65%, #c01818 100%); }
    .wire-blue   .wire-cable-body { background: linear-gradient(180deg, #4488ee 0%, #2255cc 35%, #113399 65%, #2255cc 100%); }
    .wire-green  .wire-cable-body { background: linear-gradient(180deg, #44cc44 0%, #228822 35%, #115511 65%, #228822 100%); }
    .wire-yellow .wire-cable-body { background: linear-gradient(180deg, #ffee22 0%, #ddaa00 35%, #997700 65%, #ddaa00 100%); }
    .wire-black  .wire-cable-body { background: linear-gradient(180deg, #555860 0%, #2e3035 35%, #18191c 65%, #2e3035 100%); }
    .wire-white  .wire-cable-body { background: linear-gradient(180deg, #f0f2f5 0%, #d0d3d8 35%, #a8acb2 65%, #d0d3d8 100%); }
    .wire-purple .wire-cable-body { background: linear-gradient(180deg, #9944dd 0%, #6622aa 35%, #441177 65%, #6622aa 100%); }
    .wire-orange .wire-cable-body { background: linear-gradient(180deg, #ff8822 0%, #dd5500 35%, #993300 65%, #dd5500 100%); }

    /* Listras de identificação no meio do fio */
    .wire-cable-body::before {
      content: '';
      position: absolute;
      top: 3px; bottom: 3px;
      left: 30%; right: 30%;
      border-left: 2px solid rgba(255,255,255,.12);
      border-right: 2px solid rgba(255,255,255,.12);
    }

    /* Hover e estado cortado */
    .wire-item:hover .wire-cable-body {
      filter: brightness(1.25);
      transform: scaleY(1.08);
    }
    .wire-item:active .wire-cable-body {
      filter: brightness(.7);
    }
    .wire-item.cut .wire-cable-body {
      opacity: .25;
    }
    .wire-item.cut .wire-cable-body::after {
      content: '';
      position: absolute;
      left: 50%;
      top: -4px; bottom: -4px;
      width: 4px;
      margin-left: -2px;
      background: #0d0e10;
      border-radius: 1px;
    }

    .wire-label {
      font-size: 9px;
      letter-spacing: 1px;
      color: #3a3f48;
      text-transform: uppercase;
      width: 46px;
      flex-shrink: 0;
      text-align: right;
      padding-right: 8px;
    }

    /* ── Grade de 2 colunas para os fios ── */
    .wire-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 14px 24px;
    }

    /* ── Rodapé do painel ── */
    .panel-footer {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-top: 20px;
      padding-top: 14px;
      border-top: 1px solid rgba(0,0,0,.4);
    }
    .warning-strip {
      display: flex;
      gap: 3px;
      align-items: center;
    }
    .warning-block {
      width: 18px;
      height: 10px;
      border-radius: 1px;
    }
    .warning-block:nth-child(odd)  { background: #f5c400; }
    .warning-block:nth-child(even) { background: #1a1c1f; }

    .status-indicators {
      display: flex;
      gap: 10px;
      align-items: center;
    }
    .status-led {
      width: 8px;
      height: 8px;
      border-radius: 50%;
      border: 1px solid rgba(0,0,0,.4);
    }
    .led-armed {
      background: #ff2200;
      box-shadow: 0 0 6px rgba(255,34,0,.8);
      animation: led-blink 1.2s ease-in-out infinite;
    }
    .led-safe { background: #335533; }
    @keyframes led-blink { 0%,100%{opacity:1;} 50%{opacity:.2;} }

    .status-text {
      font-size: 10px;
      letter-spacing: 2px;
      color: #ff4422;
      text-shadow: 0 0 6px rgba(255,68,34,.5);
    }
  </style>
</head>
<body>

<div class="bomb-chassis">

  <!-- Parafusos de canto -->
  <div class="screw screw-tl">
    <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="11" cy="11" r="10" fill="url(#sg)" stroke="#1a1c1f" stroke-width="1"/>
      <line x1="6" y1="11" x2="16" y2="11" stroke="#1a1c1f" stroke-width="1.5" stroke-linecap="round"/>
      <line x1="11" y1="6" x2="11" y2="16" stroke="#1a1c1f" stroke-width="1.5" stroke-linecap="round"/>
      <defs>
        <radialGradient id="sg" cx="35%" cy="30%">
          <stop offset="0%" stop-color="#9aa0a8"/>
          <stop offset="60%" stop-color="#606570"/>
          <stop offset="100%" stop-color="#3a3d42"/>
        </radialGradient>
      </defs>
    </svg>
  </div>
  <div class="screw screw-tr">
    <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="11" cy="11" r="10" fill="url(#sg2)" stroke="#1a1c1f" stroke-width="1"/>
      <line x1="6" y1="11" x2="16" y2="11" stroke="#1a1c1f" stroke-width="1.5" stroke-linecap="round"/>
      <line x1="11" y1="6" x2="11" y2="16" stroke="#1a1c1f" stroke-width="1.5" stroke-linecap="round"/>
      <defs>
        <radialGradient id="sg2" cx="35%" cy="30%">
          <stop offset="0%" stop-color="#9aa0a8"/>
          <stop offset="60%" stop-color="#606570"/>
          <stop offset="100%" stop-color="#3a3d42"/>
        </radialGradient>
      </defs>
    </svg>
  </div>
  <div class="screw screw-bl">
    <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="11" cy="11" r="10" fill="url(#sg3)" stroke="#1a1c1f" stroke-width="1"/>
      <line x1="6.5" y1="8" x2="15.5" y2="14" stroke="#1a1c1f" stroke-width="1.5" stroke-linecap="round"/>
      <line x1="6.5" y1="14" x2="15.5" y2="8" stroke="#1a1c1f" stroke-width="1.5" stroke-linecap="round"/>
      <defs>
        <radialGradient id="sg3" cx="35%" cy="30%">
          <stop offset="0%" stop-color="#9aa0a8"/>
          <stop offset="60%" stop-color="#606570"/>
          <stop offset="100%" stop-color="#3a3d42"/>
        </radialGradient>
      </defs>
    </svg>
  </div>
  <div class="screw screw-br">
    <svg viewBox="0 0 22 22" fill="none" xmlns="http://www.w3.org/2000/svg">
      <circle cx="11" cy="11" r="10" fill="url(#sg4)" stroke="#1a1c1f" stroke-width="1"/>
      <line x1="6.5" y1="8" x2="15.5" y2="14" stroke="#1a1c1f" stroke-width="1.5" stroke-linecap="round"/>
      <line x1="6.5" y1="14" x2="15.5" y2="8" stroke="#1a1c1f" stroke-width="1.5" stroke-linecap="round"/>
      <defs>
        <radialGradient id="sg4" cx="35%" cy="30%">
          <stop offset="0%" stop-color="#9aa0a8"/>
          <stop offset="60%" stop-color="#606570"/>
          <stop offset="100%" stop-color="#3a3d42"/>
        </radialGradient>
      </defs>
    </svg>
  </div>

  <!-- Seção superior -->
  <div class="top-section">

    <!-- Plaqueta de identificação -->
    <div>
      <div class="id-plate">
        EXPLOSIVE DEVICE — UNIT MK-VII
        <span class="serial">S/N: 4D-7F-C2 &nbsp;|&nbsp; CLASS: RESTRICTED &nbsp;|&nbsp; DO NOT TAMPER</span>
      </div>
    </div>

    <!-- Display LCD do timer -->
    <div class="lcd-housing">
      <div class="lcd-label">◼ DETONATION TIMER</div>
      <div class="lcd-screen">
        <div class="lcd-digits" id="timer-display">15:00</div>
      </div>
      <div class="lcd-status active" id="timer-status">● ARMED — SEQUENCE ACTIVE</div>
    </div>

  </div>

  <!-- Divisor -->
  <div class="panel-label">◼ DETONATION CIRCUIT — WIRE ARRAY</div>

  <!-- Painel de fios -->
  <div class="wire-panel">
    <div class="wire-grid" id="wire-grid">

      <div class="wire-item wire-red"    onclick="handleWire(this, 'VERMELHO')">
        <span class="wire-label">VRM</span>
        <div class="wire-terminal-left"></div>
        <div class="wire-cable"><div class="wire-cable-body"></div></div>
        <div class="wire-terminal-right"></div>
      </div>

      <div class="wire-item wire-blue"   onclick="handleWire(this, 'AZUL')">
        <span class="wire-label">AZL</span>
        <div class="wire-terminal-left"></div>
        <div class="wire-cable"><div class="wire-cable-body"></div></div>
        <div class="wire-terminal-right"></div>
      </div>

      <div class="wire-item wire-green"  onclick="handleWire(this, 'VERDE')">
        <span class="wire-label">VRD</span>
        <div class="wire-terminal-left"></div>
        <div class="wire-cable"><div class="wire-cable-body"></div></div>
        <div class="wire-terminal-right"></div>
      </div>

      <div class="wire-item wire-yellow" onclick="handleWire(this, 'AMARELO')">
        <span class="wire-label">AMR</span>
        <div class="wire-terminal-left"></div>
        <div class="wire-cable"><div class="wire-cable-body"></div></div>
        <div class="wire-terminal-right"></div>
      </div>

      <div class="wire-item wire-black"  onclick="handleWire(this, 'PRETO')">
        <span class="wire-label">PRT</span>
        <div class="wire-terminal-left"></div>
        <div class="wire-cable"><div class="wire-cable-body"></div></div>
        <div class="wire-terminal-right"></div>
      </div>

      <div class="wire-item wire-white"  onclick="handleWire(this, 'BRANCO')">
        <span class="wire-label">BRN</span>
        <div class="wire-terminal-left"></div>
        <div class="wire-cable"><div class="wire-cable-body"></div></div>
        <div class="wire-terminal-right"></div>
      </div>

      <div class="wire-item wire-purple" onclick="handleWire(this, 'ROXO')">
        <span class="wire-label">RXO</span>
        <div class="wire-terminal-left"></div>
        <div class="wire-cable"><div class="wire-cable-body"></div></div>
        <div class="wire-terminal-right"></div>
      </div>

      <div class="wire-item wire-orange" onclick="handleWire(this, 'LARANJA')">
        <span class="wire-label">LRN</span>
        <div class="wire-terminal-left"></div>
        <div class="wire-cable"><div class="wire-cable-body"></div></div>
        <div class="wire-terminal-right"></div>
      </div>

    </div>
  </div>

  <!-- Rodapé -->
  <div class="panel-footer">
    <div class="warning-strip">
      <?php for($i=0;$i<14;$i++) echo '<div class="warning-block"></div>'; ?>
    </div>
    <div class="status-indicators">
      <div class="status-led led-armed" id="armed-led"></div>
      <span class="status-text" id="armed-text">ARMED</span>
      <div class="status-led led-safe"></div>
    </div>
    <div class="warning-strip">
      <?php for($i=0;$i<14;$i++) echo '<div class="warning-block"></div>'; ?>
    </div>
  </div>

</div><!-- .bomb-chassis -->

<script>
  // ── Timer ──────────────────────────────────────────────────────────────────
  let totalSeconds = <?= $remaining ?>;
  const display  = document.getElementById('timer-display');
  const status   = document.getElementById('timer-status');
  const armedLed = document.getElementById('armed-led');
  const armedTxt = document.getElementById('armed-text');

  function pad(n) { return String(n).padStart(2, '0'); }

  function tick() {
    if (totalSeconds <= 0) {
      display.textContent = '00:00';
      display.classList.add('panic');
      status.textContent  = '■ DETONATION IMMINENT';
      return;
    }
    totalSeconds--;
    const m = Math.floor(totalSeconds / 60);
    const s = totalSeconds % 60;
    display.textContent = pad(m) + ':' + pad(s);

    if (totalSeconds <= 30) {
      display.classList.add('panic');
      status.textContent = '■ CRITICAL — ' + totalSeconds + 's REMAINING';
      armedLed.style.animationDuration = '.3s';
    }
  }

  // Renderiza o estado inicial imediatamente (evita piscar "15:00" no refresh)
  (function initDisplay() {
    const m = Math.floor(totalSeconds / 60);
    const s = totalSeconds % 60;
    display.textContent = pad(m) + ':' + pad(s);
    if (totalSeconds <= 30) {
      display.classList.add('panic');
      status.textContent = '■ CRITICAL — ' + totalSeconds + 's REMAINING';
      armedLed.style.animationDuration = '.3s';
    }
    if (totalSeconds <= 0) {
      display.textContent = '00:00';
      status.textContent  = '■ DETONATION IMMINENT';
    }
  })();

  setInterval(tick, 1000);

  // ── Fios ───────────────────────────────────────────────────────────────────
  function handleWire(el, name) {
    if (el.classList.contains('cut')) return;
    // WORK IN PROGRESS — substitua este alert pela lógica de fases
    alert('[WIP] Fio ' + name + ' selecionado.');
  }
</script>

</body>
</html>