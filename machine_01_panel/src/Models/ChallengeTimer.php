<?php
declare(strict_types=1);

final class ChallengeTimer
{
    private const TIMER_ID = '005ff71e968dab954f43c6fd124be029d049616f1c08c44fdbec298275db946e';

    public function __construct(private PDO $database)
    {
    }

    public function initialize(): void
    {
        $this->database->exec(
            'CREATE TABLE IF NOT EXISTS challenge_timer (
                id TEXT PRIMARY KEY,
                is_challenge_started INTEGER NOT NULL DEFAULT 0 CHECK (is_challenge_started IN (0, 1)),
                remaining_seconds INTEGER NOT NULL DEFAULT 0 CHECK (remaining_seconds >= 0),
                is_time_expired INTEGER NOT NULL DEFAULT 0 CHECK (is_time_expired IN (0, 1))
            )'
        );

        $statement = $this->database->prepare('DELETE FROM challenge_timer');
        $statement->execute(['id' => self::TIMER_ID]);

        $statement = $this->database->prepare(
            'INSERT INTO challenge_timer (
                id,
                is_challenge_started,
                remaining_seconds,
                is_time_expired
            ) VALUES (:id, 0, 0, 0)'
        );
        $statement->execute(['id' => self::TIMER_ID]);
    }

    public function snapshot(): array
    {
        $statement = $this->database->prepare(
            'SELECT is_challenge_started, remaining_seconds, is_time_expired
             FROM challenge_timer
             WHERE id = :id'
        );
        $statement->execute(['id' => self::TIMER_ID]);

        $row = $statement->fetch();

        if (!is_array($row)) {
            return [
                'is_challenge_started' => false,
                'remaining_seconds' => 0,
                'is_time_expired' => false,
            ];
        }

        return [
            'is_challenge_started' => (bool) $row['is_challenge_started'],
            'remaining_seconds' => max(0, (int) $row['remaining_seconds']),
            'is_time_expired' => (bool) $row['is_time_expired'],
        ];
    }

    public function start(int $durationSeconds): void
    {
        $durationSeconds = max(0, $durationSeconds);

        $statement = $this->database->prepare(
            'UPDATE challenge_timer
             SET is_challenge_started = 1,
                 remaining_seconds = :remaining_seconds,
                 is_time_expired = 0
             WHERE id = :id'
        );
        $statement->execute([
            'remaining_seconds' => $durationSeconds,
            'id' => self::TIMER_ID,
        ]);
    }

    public function decrementOneSecond(): void
    {
        $statement = $this->database->prepare(
            'UPDATE challenge_timer
             SET remaining_seconds = remaining_seconds - 1
             WHERE id = :id
               AND remaining_seconds > 0'
        );
        $statement->execute(['id' => self::TIMER_ID]);
    }

    public function applyWrongAttemptPenalty(): void
    {
        $seconds = max(0, $seconds);

        $statement = $this->database->prepare(
            'UPDATE challenge_timer
             SET remaining_seconds = :remaining_seconds
             WHERE id = :id'
        );
        $statement->execute([
            'remaining_seconds' => $seconds,
            'id' => self::TIMER_ID,
        ]);
    }

    public function markExpired(): void
    {
        $statement = $this->database->prepare(
            'UPDATE challenge_timer
             SET remaining_seconds = 0,
                 is_time_expired = 1
             WHERE id = :id'
        );
        $statement->execute(['id' => self::TIMER_ID]);
    }
}
