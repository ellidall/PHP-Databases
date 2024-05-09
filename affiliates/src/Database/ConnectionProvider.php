<?php
declare(strict_types = 1);

namespace App\Database;

use PDO;
use PDOStatement;

class ConnectionProvider
{
    public static ?PDO $connection = null;

    public function __construct(string $dsn, string $username, string $password)
    {
        if (self::$connection === null) {
            self::$connection = new PDO($dsn, $username, $password);
        }
    }

    public function execute(string $sql, array $params = []): PDOStatement
    {
        $statement = self::$connection->prepare($sql);
        $statement->execute($params);
        return $statement;
    }

    public function getLastInsertId(): int
    {
        return (int)self::$connection->lastInsertId();
    }
}