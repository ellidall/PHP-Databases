<?php
declare(strict_types = 1);

namespace App\Repository;

use App\Database\ConnectionProvider;
use App\Model\Affiliate;
use App\Model\Data\AffiliateDTO;
use PDO;

class AffiliateRepository
{
    private ConnectionProvider $connection;

    //TODO: Нужно оно соединение на скрипт, убрать из конструктора
    public function __construct()
    {
        $this->connection = new ConnectionProvider(
            $_ENV['DB_DSN'],
            $_ENV['DB_NAME'],
            $_ENV['DB_PASSWORD'],
        );
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

    /**
     * @param array{
     *     city:string,
     *     address:string,
     *     employee_count:int
     * } $affiliateData
     * @return int
     */
    public function store(array $affiliateData): int
    {
        $query = <<<SQL
            INSERT INTO affiliate (city, address, employee_count)
            VALUES (:city, :address, :employee_count)
            SQL;

        $this->connection->execute($query, [
            'city' => $affiliateData['city'],
            'address' => $affiliateData['address'],
            'employee_count' => $affiliateData['employee_count'],
        ]);

        return $this->connection->getLastInsertId();
    }

    public function update(AffiliateDTO $affiliateData): void
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
            'id' => $affiliateData->getId(),
            'city' => $affiliateData->getCity(),
            'address' => $affiliateData->getAddress(),
            'employee_count' => $affiliateData->getEmployeeCount(),
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