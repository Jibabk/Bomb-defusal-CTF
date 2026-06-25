<?php
declare(strict_types=1);

final class GreenWireLogin
{
    public function __construct(private PDO $database)
    {
        $this->initializeUsers();
    }

    public function authenticate(string $username, string $password): ?string
    {
        $sql = "SELECT username FROM green_wire_users "
            . "WHERE username = '" . $username . "' "
            . "AND password = '" . $password . "' "
            . "LIMIT 1";

        try {
            $row = $this->database->query($sql)?->fetch();
        } catch (PDOException) {
            return null;
        }

        if (!is_array($row) || !isset($row['username'])) {
            return null;
        }

        return (string) $row['username'];
    }

    private function initializeUsers(): void
    {
        $this->database->exec(
            'CREATE TABLE IF NOT EXISTS green_wire_users (
                username TEXT PRIMARY KEY,
                password TEXT NOT NULL
            )'
        );

        $statement = $this->database->prepare(
            'INSERT OR REPLACE INTO green_wire_users (username, password)
             VALUES (:username, :password)'
        );

        $statement->execute([
            'username' => 'junior',
            'password' => '3f468c4326da442be69cc7cc',
        ]);
    }
}
