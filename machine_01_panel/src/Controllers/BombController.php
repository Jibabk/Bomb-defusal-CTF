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

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $result = $this->bomb->cutWireByCode((string) ($_POST['hex_code'] ?? ''));

            switch ($result['status']) {
                case 'cut':
                    $message = 'Fio ' . $result['wire']['name'] . ' cortado.';
                    $messageType = 'success';
                    break;

                case 'already_cut':
                    $message = 'Fio ' . $result['wire']['name'] . ' já estava cortado.';
                    $messageType = 'warning';
                    break;

                case 'invalid':
                    $message = 'HEX inválido. -01:00';
                    $messageType = 'error';
                    break;

                case 'expired':
                    $message = 'Tempo esgotado.';
                    $messageType = 'error';
                    break;

                default:
                    $message = 'HEX não reconhecido. -01:00';
                    $messageType = 'error';
                    break;
            }
        }

        $remaining = $this->bomb->getRemainingSeconds();
        $isDefused = $this->bomb->isDefused();
        $isExpired = !$isDefused && $this->bomb->isExpired();

        View::render('bomb', [
            'title' => 'Bomb Defusal',
            'styles' => ['assets/css/bomb.css'],
            'scripts' => ['assets/js/timer.js', 'assets/js/wires.js'],
            'bodyAttributes' => 'data-remaining="' . $remaining . '" data-defused="' . ($isDefused ? '1' : '0') . '" data-expired="' . ($isExpired ? '1' : '0') . '"',
            'remaining' => $remaining,
            'wires' => $this->bomb->getWires(),
            'cutCount' => $this->bomb->getCutCount(),
            'totalWires' => $this->bomb->getTotalWires(),
            'isDefused' => $isDefused,
            'isExpired' => $isExpired,
            'message' => $message,
            'messageType' => $messageType,
        ]);
    }

    public function wire(string $id): void
    {
        if (!$this->bomb->isDefused() && $this->bomb->isExpired()) {
            http_response_code(403);
            echo 'Tempo esgotado.';
            return;
        }

        $wire = $this->bomb->findWire($id);

        if ($wire === null) {
            http_response_code(404);
            echo 'Página não encontrada.';
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
