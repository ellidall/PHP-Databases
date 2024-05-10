<?php
declare(strict_types=1);

namespace App\Repository;

use App\Common\GenderEnum;
use App\Database\ConnectionProvider;
use App\Model\Data\EmployeeDTO;
use App\Model\Employee;
use DateTimeImmutable;
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

    public function findById(int $id): ?Employee
    {
        $query = <<<SQL
            SELECT 
                id,
                affiliate_id,
                first_name,
                last_name,
                middle_name,
                phone_number,
                email,
                job_title,
                gender,
                birth_date,
                hire_date,
                administrator_comment
            FROM employee
            WHERE id = :id
            SQL;

        $statement = $this->connection->execute($query, [
            ':id' => $id,
        ]);
        $employee = $statement->fetch(PDO::FETCH_ASSOC);

        return $employee
            ? new Employee(
                id: $employee['id'],
                affiliateId: $employee['affiliate_id'],
                firstName: $employee['first_name'],
                lastName: $employee['last_name'],
                middleName: $employee['middle_name'],
                phone: $employee['phone_number'],
                email: $employee['email'],
                jobTitle: $employee['job_title'],
                gender: GenderEnum::from((int)$employee['gender']),
                birthDate: new DateTimeImmutable($employee['birth_date'] ?: ''),
                hireDate: new DateTimeImmutable($employee['hire_date']),
                administratorComment: $employee['administrator_comment'],
            )
            : null;
    }

    /**
     * @param array{
     *     affiliate_id: int,
     *     first_name: string,
     *     last_name: string,
     *     middle_name: string,
     *     phone_number: string,
     *     email: string,
     *     job_title: string,
     *     gender: GenderEnum,
     *     birth_date: DateTimeImmutable,
     *     hire_date: DateTimeImmutable,
     *     administrator_comment: string
     * } $employeeData
     * @return int
     */
    public function store(array $employeeData): int
    {
        $query = <<<SQL
            INSERT INTO employee (
                affiliate_id,
                first_name,
                last_name,
                middle_name,
                phone_number,
                email,
                job_title,
                gender,
                birth_date,
                hire_date,
                administrator_comment
            )
            VALUES (
                :affiliate_id,
                :first_name,
                :last_name,
                :middle_name,
                :phone_number,
                :email,
                :job_title,
                :gender,
                :birth_date,
                :hire_date,
                :administrator_comment
            )
            SQL;

        $this->connection->execute($query, [
            'affiliate_id' => $employeeData['affiliate_id'],
            'first_name' => $employeeData['first_name'],
            'last_name' => $employeeData['last_name'],
            'middle_name' => $employeeData['middle_name'] ?? null,
            'phone_number' => $employeeData['phone_number'] ?? null,
            'email' => $employeeData['email'],
            'job_title' => $employeeData['job_title'],
            'gender' => $employeeData['gender'],
            'birth_date' => (new DateTimeImmutable($employeeData['birth_date'] ?: ''))->format('Y-m-d H:i:s'),
            'hire_date' => (new DateTimeImmutable($employeeData['hire_date'] ?: ''))->format('Y-m-d H:i:s'),
            'administrator_comment' => $employeeData['administrator_comment'] ?? null,
        ]);

        return $this->connection->getLastInsertId();
    }
//TODO: объединить update и store
    public function update(EmployeeDTO $employeeData): void
    {
        $query = <<<SQL
            UPDATE employee
            SET 
                affiliate_id = :affiliate_id,
                first_name = :first_name,
                last_name = :last_name,
                middle_name = :middle_name,
                phone_number = :phone_number,
                email = :email,
                job_title = :job_title,
                gender = :gender,
                birth_date = :birth_date,
                hire_date = :hire_date,
                administrator_comment = :administrator_comment
            WHERE id = :id
            SQL;

        $this->connection->execute($query, [
            'id' => $employeeData->getId(),
            'affiliate_id' => $employeeData->getAffiliateId(),
            'first_name' => $employeeData->getFirstName(),
            'last_name' => $employeeData->getLastName(),
            'middle_name' => $employeeData->getMiddleName(),
            'phone_number' => $employeeData->getPhone(),
            'email' => $employeeData->getEmail(),
            'job_title' => $employeeData->getJobTitle(),
            'gender' => $employeeData->getGender()->value,
            'birth_date' => $employeeData->getBirthDate()->format('Y-m-d H:i:s'),
            'hire_date' => $employeeData->getHireDate()->format('Y-m-d H:i:s'),
            'administrator_comment' => $employeeData->getAdministratorComment(),
        ]);
    }

    /**
     * @param int $affiliateId
     * @return Employee[]
     */
    public function findByAffiliateId(int $affiliateId): array
    {
        $query = <<<SQL
            SELECT 
                id,
                affiliate_id,
                first_name,
                last_name,
                middle_name,
                phone_number,
                email,
                job_title,
                gender,
                birth_date,
                hire_date,
                administrator_comment
            FROM employee
            WHERE affiliate_id = :affiliate_id
            ORDER BY :affiliate_id
            SQL;

        $statement = $this->connection->execute($query, [
            'affiliate_id' => $affiliateId,
        ]);

        $employees = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC))
        {
            $employee = new Employee(
                id: $row['id'],
                affiliateId: $row['affiliate_id'],
                firstName: $row['first_name'],
                lastName: $row['last_name'],
                middleName: $row['middle_name'],
                phone: $row['phone_number'],
                email: $row['email'],
                jobTitle: $row['job_title'],
                gender: GenderEnum::from($row['gender']),
                birthDate: new DateTimeImmutable($row['birth_date'] ?: ''),
                hireDate: new DateTimeImmutable($row['hire_date']),
                administratorComment: $row['administrator_comment'],
            );
            $employees[] = $employee;
        }

        return $employees;
    }
}