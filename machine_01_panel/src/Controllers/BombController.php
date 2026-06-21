<?php
declare(strict_types=1);

final class BombController
{
    public function __construct(private Bomb $bomb)
    {
    }

    public function show(): void
    {
        $message = '';
        $messageType = '';
        $isStarted = $this->bomb->isStarted();

        if (!$isStarted) {
            http_response_code(404);
            echo 'Page not found.';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->bomb->cutWireByCode((string) ($_POST['hex_code'] ?? ''));

            switch ($result['status']) {
                case 'cut':
                    $message = 'Wire ' . $result['wire']['name'] . ' cut.';
                    $messageType = 'success';
                    break;

                case 'already_cut':
                    $message = 'Wire ' . $result['wire']['name'] . ' was already cut.';
                    $messageType = 'warning';
                    break;

                case 'invalid':
                    $message = 'Invalid HEX. -01:00';
                    $messageType = 'error';
                    break;

                case 'expired':
                    $message = 'Time expired.';
                    $messageType = 'error';
                    break;

                case 'not_started':
                    $message = 'Challenge not started.';
                    $messageType = 'warning';
                    break;

                default:
                    $message = 'Unknown HEX. -01:00';
                    $messageType = 'error';
                    break;
            }
        }

        $remaining = $this->bomb->getRemainingSeconds();
        $isDefused = $this->bomb->isDefused();
        $isExpired = $isStarted && !$isDefused && $this->bomb->isExpired();

        View::render('bomb', [
            'title' => 'Bomb Defusal',
            'styles' => ['assets/css/bomb.css'],
            'scripts' => ['assets/js/timer.js', 'assets/js/wires.js'],
            'bodyAttributes' => 'data-remaining="' . $remaining . '" data-started="' . ($isStarted ? '1' : '0') . '" data-defused="' . ($isDefused ? '1' : '0') . '" data-expired="' . ($isExpired ? '1' : '0') . '"',
            'remaining' => $remaining,
            'wires' => $this->bomb->getWires(),
            'cutCount' => $this->bomb->getCutCount(),
            'totalWires' => $this->bomb->getTotalWires(),
            'isStarted' => $isStarted,
            'isDefused' => $isDefused,
            'isExpired' => $isExpired,
            'message' => $message,
            'messageType' => $messageType,
        ]);
    }

    public function wire(string $id): void
    {
        if (!$this->bomb->isStarted() || $this->bomb->isDefused() || $this->bomb->isExpired()) {
            http_response_code(404);
            echo 'Page not found.';
            return;
        }

        $wire = $this->bomb->findWire($id);

        if ($wire === null) {
            http_response_code(404);
            echo 'Page not found.';
            return;
        }

        View::render('wire', [
            'title' => 'Wire ' . $wire['name'],
            'styles' => [],
            'scripts' => [],
            'bodyAttributes' => '',
            'wire' => $wire,
        ]);
    }
}
