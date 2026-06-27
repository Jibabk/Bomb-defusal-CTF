<?php
declare(strict_types=1);

final class Bomb
{
    private const SESSION_CUT_KEY = 'bomb_cut_wires';

    private const WIRES = [
        [
            'id' => '005ff71e968dab954f43c6fd124be029d049616f1c08c44fdbec298275db946e',
            'name' => 'RED',
            'code' => 'AF32CD',
        ],
        [
            'id' => '5c29987df4a6d17087f9ce83f86a220f6ae02a4b186255ddd007b0f9e5cfb20d',
            'name' => 'ORANGE',
            'code' => 'C0FFEE',
        ],
        [
            'id' => '68bab9978b059e97ae4fbabe62b0e96d483f4772fbbfad419e99f44c617efbe8',
            'name' => 'YELLOW',
            'code' => 'FE21DA',
        ],
        [
            'id' => '69820cbea23c67a4a28c97f457e7938fe86f506a793b74ff7700eadde1045686',
            'name' => 'GREEN',
            'code' => '071EBD',
        ],
        [
            'id' => '199a19ea7e91f903f7674f71ac3de71bcf1590e86c75c80ae6fb44f5f9ae0049',
            'name' => 'BLUE',
            'code' => 'A7F3C9',
        ],
        [
            'id' => 'd05d9d4a9011e93a6b326aa80d7ab075d7976fb8cc6f6682c3973f7b3d41eade',
            'name' => 'PINK',
            'code' => 'DEADC0',
        ],
        [
            'id' => '65091a6092f69feb2b9042146528a6f6819bf8690fea9753573e5c5273f6fcff',
            'name' => 'PURPLE',
            'code' => '4C9E2B',
        ],
        [
            'id' => 'c903f7d76b38fd80c8dcb56c2707e5c39c8b39fbec0df00dea80cca28c4d1057',
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

            if (count($_SESSION[self::SESSION_CUT_KEY]) >= count(self::WIRES)) {
                $this->timer->markDefused();
            }

            return 'cut';
        }

        $this->applyWrongAttemptPenalty();

        return 'not_found';
    }

    public function isDefused(): bool
    {
        return $this->timer->snapshot()['is_challenge_defused']
            || count($this->getCutWireIds()) >= count(self::WIRES);
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
