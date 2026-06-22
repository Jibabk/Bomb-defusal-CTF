<?php
declare(strict_types=1);

final class Database
{
    private static ?PDO $connection = null;

    public static function connection(): PDO
    {
        if (self::$connection !== null) {
            return self::$connection;
        }

        $databasePath = getenv('BOMB_DB_PATH') ?: __DIR__ . '/../storage/bomb.sqlite';
        $databaseDirectory = dirname($databasePath);

        if (!is_dir($databaseDirectory) && !mkdir($databaseDirectory, 0775, true) && !is_dir($databaseDirectory)) {
            throw new RuntimeException('Unable to create database directory.');
        }

        self::$connection = new PDO('sqlite:' . $databasePath);
        self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$connection->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        self::$connection->exec('PRAGMA busy_timeout = 5000');
        self::$connection->exec('PRAGMA journal_mode = WAL');

        return self::$connection;
    }
}
