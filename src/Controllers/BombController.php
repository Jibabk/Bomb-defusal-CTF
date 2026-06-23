<?php
declare(strict_types=1);

final class BombController
{
    public function __construct(private Bomb $bomb)
    {
    }

    public function show(): void
    {
        $audioEvent = '';
        $timerStatus = $this->bomb->getTimerStatus();
        $isStarted = $timerStatus['is_challenge_started'];

        if (!$isStarted) {
            http_response_code(404);
            echo 'Page not found.';
            return;
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->bomb->cutWireByCode((string) ($_POST['hex_code'] ?? ''));

            if (in_array($result, ['invalid', 'not_found'], true)) {
                $audioEvent = 'error';
            }
        }

        $timerStatus = $this->bomb->getTimerStatus();
        $remaining = $timerStatus['remaining_seconds'];
        $isDefused = $this->bomb->isDefused();
        $isExpired = $isStarted && !$isDefused && $timerStatus['is_time_expired'];

        if (
            $_SERVER['REQUEST_METHOD'] === 'POST'
            && in_array(($result ?? ''), ['invalid', 'not_found'], true)
            && $isExpired
        ) {
            $audioEvent = 'explosion';
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($result ?? '') === 'cut') {
            $audioEvent = $isDefused ? 'defuse' : 'success';
        }

        View::render('bomb', [
            'title' => 'Bomb Defusal',
            'styles' => ['Assets/css/bomb.css'],
            'scripts' => ['Assets/js/audio.js', 'Assets/js/timer.js', 'Assets/js/wires.js'],
            'bodyAttributes' => 'data-remaining="' . $remaining . '" data-started="' . ($isStarted ? '1' : '0') . '" data-defused="' . ($isDefused ? '1' : '0') . '" data-expired="' . ($isExpired ? '1' : '0') . '" data-audio-event="' . $audioEvent . '"',
            'wires' => $this->bomb->getWires(),
            'isStarted' => $isStarted,
            'isDefused' => $isDefused,
            'isExpired' => $isExpired,
        ]);
    }

    public function timer(): void
    {
        $timerStatus = $this->bomb->getTimerStatus();
        $isDefused = $this->bomb->isDefused();

        header('Content-Type: application/json');
        echo json_encode([
            'remainingSeconds' => $timerStatus['remaining_seconds'],
            'isStarted' => $timerStatus['is_challenge_started'],
            'isDefused' => $isDefused,
            'isExpired' => $timerStatus['is_challenge_started']
                && !$isDefused
                && $timerStatus['is_time_expired'],
        ], JSON_THROW_ON_ERROR);
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

        $wireContent = match (strtolower($wire['name'])) {
            'red' => $this->readChallengeFile('base64.txt', 'red'),
            'orange' => $this->readChallengeFile('hash.txt', 'orange'),
            default => $wire['code'],
        };

        View::render('wire', [
            'title' => $wire['name'],
            'styles' => [],
            'scripts' => [],
            'bodyAttributes' => '',
            'wire' => $wire,
            'wireContent' => $wireContent,
        ]);
    }

    private function readChallengeFile(string $filename, string $wireName): string
    {
        $content = file_get_contents(__DIR__ . '/../Content/' . $filename);

        if ($content === false) {
            throw new RuntimeException('Unable to read ' . $wireName . ' wire challenge.');
        }

        return $content;
    }
}
