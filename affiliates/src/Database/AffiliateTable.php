<?php
declare(strict_types = 1);

namespace App\Database;

use App\Common\Connection;
use App\Model\Affiliate;
use PDO;

class AffiliateTable
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function findById(int $id): ?Affiliate
    {
        $query = <<<SQL
            SELECT 
                id,
                city,
                address,
                employee_count
            FROM affiliate
            WHERE id = :id
            SQL;

        $statement = $this->connection->execute($query, [
            ':id' => $id,
        ]);
        $affiliate = $statement->fetch(PDO::FETCH_ASSOC);

        return $affiliate
            ? new Affiliate(
                $affiliate['id'],
                $affiliate['city'],
                $affiliate['address'],
                $affiliate['employee_count'],
            )
            : null;
    }

    public function create(Affiliate $affiliate): int
    {
        $query = <<<SQL
            INSERT INTO affiliate (city, address, employee_count)
            VALUES (:city, :address, :employee_count)
            SQL;

        $this->connection->execute($query, [
            'city' => $affiliate->getCity(),
            'address' => $affiliate->getAddress(),
            'employee_count' => $affiliate->getEmployeeCount(),
        ]);

        return $this->connection->getLastInsertId();
    }

    public function update(Affiliate $affiliate): void
    {
        $query = <<<SQL
            UPDATE affiliate
            SET 
                city = :city,
                address = :address,
                employee_count = :employee_count
            WHERE id = :id
            SQL;

        $this->connection->execute($query, [
            'id' => $affiliate->getId(),
            'city' => $affiliate->getCity(),
            'address' => $affiliate->getAddress(),
            'employee_count' => $affiliate->getEmployeeCount(),
        ]);
    }

    public function delete(Affiliate $affiliate): void
    {
        $query = <<<SQL
            DELETE FROM affiliate 
            WHERE id = :id
            SQL;

        $id = $affiliate->getId();
            $this->connection->execute($query, [
            'id' => $affiliate->getId(),
        ]);
    }

    /**
     * @return Affiliate[]
     */
    public function listAll(): array
    {
        $query = <<<SQL
            SELECT 
                id,
                city,
                address,
                employee_count
            FROM affiliate
            ORDER BY id
            SQL;

        $statement = $this->connection->execute($query);
        $affiliates = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC))
        {
            $affiliate = new Affiliate(
                $row['id'],
                $row['city'],
                $row['address'],
                $row['employee_count']
            );
            $affiliates[] = $affiliate;
        }

        return $affiliates;
    }
}