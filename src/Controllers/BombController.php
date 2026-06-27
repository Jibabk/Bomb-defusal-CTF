<?php
declare(strict_types=1);

final class BombController
{
    private const AUTHORIZED_AGENT = 'BombDefuser';
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

        $wireName = strtolower($wire['name']);
        $wireContent = match ($wireName) {
            'red' => $this->readChallengeFile('base64.txt', 'red'),
            'orange' => $this->readChallengeFile('hash.txt', 'orange'),
            'yellow' => '',
            'green' => $this->greenWireContent($wire),
            'blue' => '',
            'pink' => '',
            'purple' => $wire['code'],
            'cyan' => '',
        };

        $styles = match ($wireName) {
            'yellow' => ['Assets/css/wires/yellow.css'],
            'green' => ['Assets/css/wires/green.css'],
            default => [],
        };
        $scripts = match ($wireName) {
            'green' => ['Assets/js/wires/green.js'],
            default => [],
        };

        if ($wire['name'] === 'PURPLE') {
            $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? '';
 
            if ($userAgent !== self::AUTHORIZED_AGENT) {
                http_response_code(403);
                View::render('denied', [
                    'title'          => 'Access Denied',
                    'styles'         => [],
                    'scripts'        => [],
                    'bodyAttributes' => '',
                    'userAgent'      => $userAgent,
                ]);
                return;
            }

        }

        View::render('wire', [
            'title' => $wire['name'],
            'styles' => $styles,
            'scripts' => $scripts,
            'bodyAttributes' => 'class="wire-page"',
            'wire' => $wire,
            'wireContent' => $wireContent,
        ]);
    }

    public function secret(): void
    {
        $id='199a19ea7e91f903f7674f71ac3de71bcf1590e86c75c80ae6fb44f5f9ae0049'; 
        if (!$this->bomb->isStarted() || $this->bomb->isDefused() || $this->bomb->isExpired()) {
            http_response_code(404);
            echo 'Page not found.';
            return;
        }

        $wire = $this->bomb->findWire($id);
        $styles = [];
        $scripts = [];

        View::render('secret', [
            'title' => $wire['name'],
            'styles' => $styles,
            'scripts' => $scripts,
            'bodyAttributes' => 'class="wire-page"',
            'wire' => $wire,
            'wireContent' => $wire['code'],
        ]);
    }

    public function defuseCode(): void
    {
        header('Content-Type: application/json');
        if (!$this->bomb->isDefused()) {
            http_response_code(403);
            echo json_encode(['error' => 'Forbidden']);
            return;
        }
        echo json_encode(['code' => 'D3F347']);
    }

    private function readChallengeFile(string $filename, string $wireName): string
    {
        $content = file_get_contents(__DIR__ . '/../public/Content/' . $filename);

        if ($content === false) {
            throw new RuntimeException('Unable to read ' . $wireName . ' wire challenge.');
        }

        return $content;
    }

    private function greenWireContent(array $wire): string
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            return '';
        }

        $login = new GreenWireLogin(Database::connection());
        $user = $login->authenticate(
            (string) ($_POST['username'] ?? ''),
            (string) ($_POST['password'] ?? '')
        );

        return $user === null ? '' : $wire['code'];
    }
}
