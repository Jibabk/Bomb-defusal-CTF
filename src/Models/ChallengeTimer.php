<?php
declare(strict_types=1);

final class ChallengeTimer
{
    private const TIMER_ID = '005ff71e968dab954f43c6fd124be029d049616f1c08c44fdbec298275db946e';
    private const INITIAL_SECONDS = 50 * 60;

    public function __construct(private PDO $database)
    {
    }

    public function initialize(): void
    {
        $this->database->exec('DROP TABLE IF EXISTS challenge_timer');

        $this->database->exec(
            'CREATE TABLE challenge_timer (
                id TEXT PRIMARY KEY,
                is_challenge_started INTEGER NOT NULL DEFAULT 0 CHECK (is_challenge_started IN (0, 1)),
                remaining_seconds INTEGER NOT NULL DEFAULT 3000,
                is_time_expired INTEGER NOT NULL DEFAULT 0 CHECK (is_time_expired IN (0, 1)),
                is_challenge_defused INTEGER NOT NULL DEFAULT 0 CHECK (is_challenge_defused IN (0, 1))
            )'
        );

        $statement = $this->database->prepare(
            'INSERT INTO challenge_timer (
                id,
                is_challenge_started,
                remaining_seconds,
                is_time_expired
            ) VALUES (:id, 0, :remaining_seconds, 0)'
        );

        $statement->execute([
            'id' => self::TIMER_ID,
            'remaining_seconds' => self::INITIAL_SECONDS,
        ]);
    }

    public function snapshot(): array
    {
        $statement = $this->database->prepare(
            'SELECT is_challenge_started, remaining_seconds, is_time_expired, is_challenge_defused
             FROM challenge_timer
             WHERE id = :id'
        );
        $statement->execute(['id' => self::TIMER_ID]);

        $row = $statement->fetch();

        return [
            'is_challenge_started' => (bool) $row['is_challenge_started'],
            'remaining_seconds' => (int) $row['remaining_seconds'],
            'is_time_expired' => (bool) $row['is_time_expired'],
            'is_challenge_defused' => (bool) $row['is_challenge_defused'],
        ];
    }

    public function start(): void
    {
        $statement = $this->database->prepare(
            'UPDATE challenge_timer
             SET is_challenge_started = 1,
                 is_time_expired = 0
             WHERE id = :id'
        );
        $statement->execute(['id' => self::TIMER_ID]);
    }

    public function decrementOneSecond(): void
    {
        $statement = $this->database->prepare(
            'UPDATE challenge_timer
             SET remaining_seconds = remaining_seconds - 1
             WHERE id = :id'
        );
        $statement->execute(['id' => self::TIMER_ID]);
    }

    public function applyWrongAttemptPenalty(): void
    {
        $statement = $this->database->prepare(
            'UPDATE challenge_timer
             SET remaining_seconds = remaining_seconds - 60
             WHERE id = :id'
        );
        $statement->execute(['id' => self::TIMER_ID]);
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

    public function markDefused(): void
    {
        $statement = $this->database->prepare(
            'UPDATE challenge_timer
             SET is_challenge_defused = 1
             WHERE id = :id'
        );
        $statement->execute(['id' => self::TIMER_ID]);
    }
}
