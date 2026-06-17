<?php
$keypadKeys = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F'];
$panelLocked = $isDefused || $isExpired;
$buttonLabel = $isDefused ? 'Bomba desarmada' : ($isExpired ? 'Tempo esgotado' : 'Cortar fio');
?>

<main class="bomb-stage">
    <section class="bomb-chassis<?= $isDefused ? ' is-defused' : '' ?><?= $isExpired ? ' is-expired' : '' ?>" aria-label="Painel da bomba">
        <span class="case-screw screw-tl"></span>
        <span class="case-screw screw-tr"></span>
        <span class="case-screw screw-bl"></span>
        <span class="case-screw screw-br"></span>

        <div class="bomb-layout">
            <section class="wire-bay" aria-label="Fios">
                <div class="wire-rack">
                    <?php foreach ($wires as $wire): ?>
                        <?php
                        $wireDisabled = $isExpired || $isDefused || $wire['cut'];
                        $wireClass = 'wire-item wire-' . htmlspecialchars($wire['id'], ENT_QUOTES, 'UTF-8') . ($wire['cut'] ? ' is-cut' : '') . ($wireDisabled ? ' is-disabled' : '');
                        $wireName = htmlspecialchars($wire['name'], ENT_QUOTES, 'UTF-8');
                        ?>
                        <?php if ($wireDisabled): ?>
                            <span
                                class="<?= $wireClass ?>"
                                data-wire="<?= $wireName ?>"
                                aria-label="Fio <?= $wireName ?>"
                                aria-disabled="true"
                            >
                        <?php else: ?>
                            <a
                                class="<?= $wireClass ?>"
                                href="index.php?route=wire&amp;id=<?= rawurlencode($wire['id']) ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                                data-wire="<?= $wireName ?>"
                                data-wire-link
                                aria-label="Fio <?= $wireName ?>"
                            >
                        <?php endif; ?>
                            <span class="wire-screw wire-screw-left"></span>
                            <span class="wire-terminal"></span>
                            <span class="wire-cable" aria-hidden="true">
                                <svg viewBox="0 0 220 34" preserveAspectRatio="none" focusable="false">
                                    <path class="wire-shadow wire-whole" d="M4 17 C42 7 70 27 110 17 S178 7 216 17"></path>
                                    <path class="wire-core wire-whole" d="M4 17 C42 7 70 27 110 17 S178 7 216 17"></path>
                                    <path class="wire-shine wire-whole" d="M4 13 C42 3 70 23 110 13 S178 3 216 13"></path>
                                    <path class="wire-shadow wire-cut-left" d="M4 17 C42 7 70 27 100 18"></path>
                                    <path class="wire-core wire-cut-left" d="M4 17 C42 7 70 27 100 18"></path>
                                    <path class="wire-shadow wire-cut-right" d="M120 16 C150 7 178 7 216 17"></path>
                                    <path class="wire-core wire-cut-right" d="M120 16 C150 7 178 7 216 17"></path>
                                </svg>
                            </span>
                            <span class="wire-terminal"></span>
                            <span class="wire-screw wire-screw-right"></span>
                        <?= $wireDisabled ? '</span>' : '</a>' ?>
                    <?php endforeach; ?>
                </div>
            </section>

            <section class="center-column" aria-label="Acionamento central">
                <button
                    class="defuse-button<?= $isDefused ? ' is-safe' : '' ?><?= $isExpired ? ' is-expired' : '' ?>"
                    id="defuse-button"
                    type="submit"
                    form="defuse-form"
                    aria-label="<?= htmlspecialchars($buttonLabel, ENT_QUOTES, 'UTF-8') ?>"
                    <?= $panelLocked ? 'disabled' : '' ?>
                >
                    <span class="defuse-light"></span>
                </button>

                <div class="vent-panel" aria-hidden="true">
                    <?php for ($i = 0; $i < 8; $i++): ?>
                        <span></span>
                    <?php endfor; ?>
                </div>
            </section>

            <section class="control-panel" aria-label="Controles">
                <div class="timer-screen">
                    <span class="lcd-digits" id="timer-display">50:00</span>
                </div>

                <form class="hex-form" id="defuse-form" method="post" action="index.php?route=bomb" autocomplete="off">
                    <input
                        class="hex-input"
                        id="hex-input"
                        name="hex_code"
                        type="text"
                        inputmode="none"
                        maxlength="6"
                        aria-label="HEX do fio"
                        readonly
                        <?= $panelLocked ? 'disabled' : '' ?>
                    >
                    <span class="entry-preview" id="entry-preview" aria-hidden="true">_</span>
                </form>

                <div class="keypad-grid" aria-label="Teclado hexadecimal">
                    <?php foreach ($keypadKeys as $key): ?>
                        <button class="hex-key" type="button" data-key="<?= $key ?>" <?= $panelLocked ? 'disabled' : '' ?>>
                            <?= $key ?>
                        </button>
                    <?php endforeach; ?>
                    <button class="hex-key clear-key" type="button" data-clear <?= $panelLocked ? 'disabled' : '' ?>>
                        CLR
                    </button>
                </div>
            </section>
        </div>
    </section>
</main>
