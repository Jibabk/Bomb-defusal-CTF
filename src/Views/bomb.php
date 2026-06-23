<?php
$keypadKeys = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'A', 'B', 'C', 'D', 'E', 'F'];
$panelLocked = !$isStarted || $isDefused || $isExpired;
$buttonLabel = !$isStarted ? 'Challenge not started' : ($isDefused ? 'Bomb defused' : ($isExpired ? 'Time expired' : 'Cut wire'));
?>

<main class="bomb-stage">
    <section class="bomb-chassis<?= $isDefused ? ' is-defused' : '' ?><?= $isExpired ? ' is-expired' : '' ?>" aria-label="Bomb panel">
        <span class="case-screw screw-tl"></span>
        <span class="case-screw screw-tr"></span>
        <span class="case-screw screw-bl"></span>
        <span class="case-screw screw-br"></span>

        <div class="bomb-layout">
            <section class="wire-bay" aria-label="Wires">
                <div class="wire-rack">
                    <?php foreach ($wires as $wire): ?>
                        <?php
                        $wireDisabled = !$isStarted || $isExpired || $isDefused || $wire['cut'];
                        $wireName = htmlspecialchars($wire['name'], ENT_QUOTES, 'UTF-8');
                        $wireClassName = htmlspecialchars(strtolower($wire['name']), ENT_QUOTES, 'UTF-8');
                        $wireClass = 'wire-item wire-' . $wireClassName . ($wire['cut'] ? ' is-cut' : '') . ($wireDisabled ? ' is-disabled' : '');
                        ?>
                        <?php if ($wireDisabled): ?>
                            <span
                                class="<?= $wireClass ?>"
                                data-wire="<?= $wireName ?>"
                                aria-label="Wire <?= $wireName ?>"
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
                                aria-label="Wire <?= $wireName ?>"
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

            <section class="center-column" aria-label="Central trigger">
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

            <section class="control-panel" aria-label="Controls">
                <div class="timer-screen">
                    <span class="lcd-digits" id="timer-display">50:00</span>
                </div>

                <div class="hex-entry-row">
                    <form class="hex-form" id="defuse-form" method="post" action="index.php?route=bomb" autocomplete="off">
                        <input
                            class="hex-input"
                            id="hex-input"
                            name="hex_code"
                            type="text"
                            inputmode="none"
                            maxlength="6"
                            aria-label="Wire HEX"
                            readonly
                            <?= $panelLocked ? 'disabled' : '' ?>
                        >
                        <span class="entry-preview" id="entry-preview" aria-hidden="true">_</span>
                    </form>

                    <button class="hex-key clear-key" type="button" data-clear <?= $panelLocked ? 'disabled' : '' ?>>
                        CLR
                    </button>
                </div>

                <div class="keypad-grid" aria-label="Hexadecimal keypad">
                    <?php foreach ($keypadKeys as $key): ?>
                        <button class="hex-key" type="button" data-key="<?= $key ?>" <?= $panelLocked ? 'disabled' : '' ?>>
                            <?= $key ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </section>
        </div>
    </section>
</main>
