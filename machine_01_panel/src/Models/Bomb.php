<?php
declare(strict_types=1);

final class Bomb
{
    private const DURATION_SECONDS = 3000;
    private const SESSION_START_KEY = 'bomb_start';
    private const SESSION_CUT_KEY = 'bomb_cut_wires';

    private const WIRES = [
        [
            'id' => 'red',
            'name' => 'VERMELHO',
            'code' => 'A1B2C3',
        ],
        [
            'id' => 'orange',
            'name' => 'LARANJA',
            'code' => '9F8E7D',
        ],
        [
            'id' => 'yellow',
            'name' => 'AMARELO',
            'code' => 'FFEEDD',
        ],
        [
            'id' => 'green',
            'name' => 'VERDE',
            'code' => '0F1E2D',
        ],
        [
            'id' => 'blue',
            'name' => 'AZUL',
            'code' => '1234AB',
        ],
        [
            'id' => 'purple',
            'name' => 'ROXO',
            'code' => 'C0FFEE',
        ],
        [
            'id' => 'pink',
            'name' => 'ROSA',
            'code' => 'DEADC0',
        ],
        [
            'id' => 'cyan',
            'name' => 'CIANO',
            'code' => '5AFECA',
        ],
    ];

    public function startIfNeeded(): void
    {
        if (!isset($_SESSION[self::SESSION_START_KEY])) {
            $_SESSION[self::SESSION_START_KEY] = time();
        }
    }

    public function getRemainingSeconds(): int
    {
        $this->startIfNeeded();

        $elapsed = time() - (int) $_SESSION[self::SESSION_START_KEY];

        return max(0, self::DURATION_SECONDS - $elapsed);
    }

    public function isExpired(): bool
    {
        return $this->getRemainingSeconds() <= 0;
    }

    public function getWires(): array
    {
        return array_map(function (array $wire): array {
            $wire['cut'] = $this->isWireCut($wire['id']);

            return $wire;
        }, self::WIRES);
    }

    public function findWire(string $id): ?array
    {
        foreach ($this->getWires() as $wire) {
            if ($wire['id'] === $id) {
                return $wire;
            }
        }

        return null;
    }

    public function cutWireByCode(string $code): array
    {
        if ($this->isExpired()) {
            return ['status' => 'expired'];
        }

        $normalizedCode = strtoupper(trim($code));

        if (!preg_match('/^[0-9A-F]{6}$/', $normalizedCode)) {
            $this->applyWrongAttemptPenalty();

            return ['status' => 'invalid'];
        }

        foreach (self::WIRES as $wire) {
            if (!hash_equals($wire['code'], $normalizedCode)) {
                continue;
            }

            if ($this->isWireCut($wire['id'])) {
                return [
                    'status' => 'already_cut',
                    'wire' => $wire,
                ];
            }

            $cutWires = $this->getCutWireIds();
            $cutWires[] = $wire['id'];
            $_SESSION[self::SESSION_CUT_KEY] = array_values(array_unique($cutWires));

            return [
                'status' => 'cut',
                'wire' => $wire,
            ];
        }

        $this->applyWrongAttemptPenalty();

        return ['status' => 'not_found'];
    }

    public function getCutCount(): int
    {
        return count($this->getCutWireIds());
    }

    public function getTotalWires(): int
    {
        return count(self::WIRES);
    }

    public function isDefused(): bool
    {
        return $this->getCutCount() >= $this->getTotalWires();
    }

    public function reset(): void
    {
        unset($_SESSION[self::SESSION_START_KEY]);
        unset($_SESSION[self::SESSION_CUT_KEY]);
    }

    private function getCutWireIds(): array
    {
        $cutWires = $_SESSION[self::SESSION_CUT_KEY] ?? [];

        return is_array($cutWires) ? $cutWires : [];
    }

    private function isWireCut(string $id): bool
    {
        return in_array($id, $this->getCutWireIds(), true);
    }

    private function applyWrongAttemptPenalty(): void
    {
        $this->startIfNeeded();

        $_SESSION[self::SESSION_START_KEY] = (int) $_SESSION[self::SESSION_START_KEY] - 60;
    }
}
