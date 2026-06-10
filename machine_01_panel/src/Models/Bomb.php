<?php
declare(strict_types=1);

final class Bomb
{
    private const DURATION_SECONDS = 10;
    private const SESSION_START_KEY = 'bomb_start';

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

    public function reset(): void
    {
        unset($_SESSION[self::SESSION_START_KEY]);
    }
}
