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

      <div class="wire-item wire-red" data-wire="VERMELHO">
        <span class="wire-label">VRM</span>
        <div class="wire-terminal-left"></div>
        <div class="wire-cable"><div class="wire-cable-body"></div></div>
        <div class="wire-terminal-right"></div>
      </div>

      <div class="wire-item wire-blue" data-wire="AZUL">
        <span class="wire-label">AZL</span>
        <div class="wire-terminal-left"></div>
        <div class="wire-cable"><div class="wire-cable-body"></div></div>
        <div class="wire-terminal-right"></div>
      </div>

      <div class="wire-item wire-green" data-wire="VERDE">
        <span class="wire-label">VRD</span>
        <div class="wire-terminal-left"></div>
        <div class="wire-cable"><div class="wire-cable-body"></div></div>
        <div class="wire-terminal-right"></div>
      </div>

      <div class="wire-item wire-yellow" data-wire="AMARELO">
        <span class="wire-label">AMR</span>
        <div class="wire-terminal-left"></div>
        <div class="wire-cable"><div class="wire-cable-body"></div></div>
        <div class="wire-terminal-right"></div>
      </div>

      <div class="wire-item wire-black" data-wire="PRETO">
        <span class="wire-label">PRT</span>
        <div class="wire-terminal-left"></div>
        <div class="wire-cable"><div class="wire-cable-body"></div></div>
        <div class="wire-terminal-right"></div>
      </div>

      <div class="wire-item wire-white" data-wire="BRANCO">
        <span class="wire-label">BRN</span>
        <div class="wire-terminal-left"></div>
        <div class="wire-cable"><div class="wire-cable-body"></div></div>
        <div class="wire-terminal-right"></div>
      </div>

      <div class="wire-item wire-purple" data-wire="ROXO">
        <span class="wire-label">RXO</span>
        <div class="wire-terminal-left"></div>
        <div class="wire-cable"><div class="wire-cable-body"></div></div>
        <div class="wire-terminal-right"></div>
      </div>

      <div class="wire-item wire-orange" data-wire="LARANJA">
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
