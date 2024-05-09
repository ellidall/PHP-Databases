<?php
declare(strict_types=1);

namespace App\Repository;

use App\Database\ConnectionProvider;
use App\Model\Affiliate;
use App\Model\Employee;
use PDO;

class EmployeeRepository
{
    private ConnectionProvider $connection;

    public function __construct()
    {
        $this->connection = new ConnectionProvider(
            $_ENV['DB_DSN'],
            $_ENV['DB_NAME'],
            $_ENV['DB_PASSWORD'],
        );
    }

    /**
     * @return Employee[]
     */
    public function listAll(): array
    {
        $query = <<<SQL
            /*TODO: get employees, not affiliates*/
            SELECT *
            FROM affiliate
            ORDER BY id DESC
            SQL;

        $statement = $this->connection->execute($query);
        $employees = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC))
        {
            /*$employee = new Employee(

            );*/
            $employee = null;
            $employees[] = $employee;
        }

        return $employees;
    }
}