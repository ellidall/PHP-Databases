<?php
declare(strict_types=1);

namespace App\Database;

use App\Common\Database\Connection;
use App\Common\GenderEnum;
use App\Model\Employee;
use DateTimeImmutable;
use Exception;
use PDO;

class EmployeeTable
{
    private Connection $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param int $id
     * @return Employee|null
     * @throws Exception
     */
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
                administrator_comment,
                avatar
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
                avatar: $employee['avatar'],
            )
            : null;
    }

    /**
     * @param int $affiliateId
     * @return Employee[]
     * @throws Exception
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
                administrator_comment,
                avatar
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
                avatar: $row['avatar'],
            );
            $employees[] = $employee;
        }

        return $employees;
    }

    public function insert(Employee $employee): int
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
                administrator_comment,
                avatar
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
                :administrator_comment,
                :avatar
            )
            SQL;

        $this->connection->execute($query, [
            'affiliate_id' => $employee->getAffiliateId(),
            'first_name' => $employee->getFirstName(),
            'last_name' => $employee->getLastName(),
            'middle_name' => $employee->getMiddleName(),
            'phone_number' => $employee->getPhone(),
            'email' => $employee->getEmail(),
            'job_title' => $employee->getJobTitle(),
            'gender' => $employee->getGender()->value,
            'birth_date' => $employee->getBirthDate()->format('Y-m-d H:i:s'),
            'hire_date' => $employee->getHireDate()->format('Y-m-d H:i:s'),
            'administrator_comment' => $employee->getAdministratorComment(),
            'avatar' => $employee->getAvatar(),
        ]);

        return $this->connection->getLastInsertId();
    }

    public function update(Employee $employee): void
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
                administrator_comment = :administrator_comment,
                avatar = :avatar
            WHERE id = :id
            SQL;

        $this->connection->execute($query, [
            'id' => $employee->getId(),
            'affiliate_id' => $employee->getAffiliateId(),
            'first_name' => $employee->getFirstName(),
            'last_name' => $employee->getLastName(),
            'middle_name' => $employee->getMiddleName(),
            'phone_number' => $employee->getPhone(),
            'email' => $employee->getEmail(),
            'job_title' => $employee->getJobTitle(),
            'gender' => $employee->getGender()->value,
            'birth_date' => $employee->getBirthDate()->format('Y-m-d H:i:s'),
            'hire_date' => $employee->getHireDate()->format('Y-m-d H:i:s'),
            'administrator_comment' => $employee->getAdministratorComment(),
            'avatar' => $employee->getAvatar(),
        ]);
    }

    public function delete(Employee $employee): void
    {
        $query = <<<SQL
            DELETE FROM employee
            WHERE id = :id
            SQL;

        $this->connection->execute($query, [
            'id' => $employee->getId(),
        ]);
    }
}