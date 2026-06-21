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
            'name' => 'RED',
            'code' => 'A1B2C3',
        ],
        [
            'id' => 'orange',
            'name' => 'ORANGE',
            'code' => '9F8E7D',
        ],
        [
            'id' => 'yellow',
            'name' => 'YELLOW',
            'code' => 'FFEEDD',
        ],
        [
            'id' => 'green',
            'name' => 'GREEN',
            'code' => '0F1E2D',
        ],
        [
            'id' => 'blue',
            'name' => 'BLUE',
            'code' => '1234AB',
        ],
        [
            'id' => 'purple',
            'name' => 'PURPLE',
            'code' => 'C0FFEE',
        ],
        [
            'id' => 'pink',
            'name' => 'PINK',
            'code' => 'DEADC0',
        ],
        [
            'id' => 'cyan',
            'name' => 'CYAN',
            'code' => '5AFECA',
        ],
    ];

    public function start(): void
    {
        $_SESSION[self::SESSION_START_KEY] = time();
        $_SESSION[self::SESSION_CUT_KEY] = [];
    }

    public function isStarted(): bool
    {
        return isset($_SESSION[self::SESSION_START_KEY]);
    }

    public function getRemainingSeconds(): int
    {
        if (!$this->isStarted()) {
            return self::DURATION_SECONDS;
        }

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
        if (!$this->isStarted()) {
            return ['status' => 'not_started'];
        }

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
        $_SESSION[self::SESSION_START_KEY] = (int) $_SESSION[self::SESSION_START_KEY] - 60;
    }
}
