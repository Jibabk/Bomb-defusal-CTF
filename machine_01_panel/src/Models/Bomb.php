<?php
declare(strict_types=1);

final class Bomb
{
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

    public function __construct(private ?ChallengeTimer $timer = null)
    {
        $this->timer ??= new ChallengeTimer(Database::connection());
    }

    public function start(): void
    {
        if ($this->timer->snapshot()['is_challenge_started']) {
            return;
        }

        $this->timer->start();
        $_SESSION[self::SESSION_CUT_KEY] = [];
    }

    public function isStarted(): bool
    {
        return $this->timer->snapshot()['is_challenge_started'];
    }

    public function isExpired(): bool
    {
        $timerStatus = $this->timer->snapshot();

        return $timerStatus['is_challenge_started']
            && ($timerStatus['is_time_expired'] || $timerStatus['remaining_seconds'] <= 0);
    }

    public function getTimerStatus(): array
    {
        $timerStatus = $this->timer->snapshot();

        $timerStatus['is_time_expired'] = $timerStatus['is_challenge_started']
            && ($timerStatus['is_time_expired'] || $timerStatus['remaining_seconds'] <= 0);

        return $timerStatus;
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

    public function cutWireByCode(string $code): string
    {
        if (!$this->isStarted()) {
            return 'not_started';
        }

        if ($this->isExpired()) {
            return 'expired';
        }

        $normalizedCode = strtoupper(trim($code));

        if (!preg_match('/^[0-9A-F]{6}$/', $normalizedCode)) {
            $this->applyWrongAttemptPenalty();

            return 'invalid';
        }

        foreach (self::WIRES as $wire) {
            if (!hash_equals($wire['code'], $normalizedCode)) {
                continue;
            }

            if ($this->isWireCut($wire['id'])) {
                return 'already_cut';
            }

            $cutWires = $this->getCutWireIds();
            $cutWires[] = $wire['id'];
            $_SESSION[self::SESSION_CUT_KEY] = array_values(array_unique($cutWires));

            return 'cut';
        }

        $this->applyWrongAttemptPenalty();

        return 'not_found';
    }

    public function isDefused(): bool
    {
        return count($this->getCutWireIds()) >= count(self::WIRES);
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
        $this->timer->applyWrongAttemptPenalty();
    }
}
