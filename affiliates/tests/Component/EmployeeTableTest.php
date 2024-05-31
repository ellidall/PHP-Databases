<?php
declare(strict_types=1);

namespace App\Tests\Component;

use App\Common\GenderEnum;
use App\Database\AffiliateTable;
use App\Database\EmployeeTable;
use App\Model\Affiliate;
use App\Model\Employee;
use App\Tests\Common\AbstractDatabaseTestCase;
use DateTimeImmutable;

class EmployeeTableTest extends AbstractDatabaseTestCase
{
    public function testCreateEditAndDeleteEmployee(): void
    {
        $employeeTable = $this->createEmployeeTable();
        $affiliateTable = $this->createAffiliateTable();
        $affiliateId = $affiliateTable->insert(new Affiliate(
            id: null,
            city: 'Йошкар-Ола',
            address: 'ул. Строителей, д.99',
            employeeCount: 10,
        ));

        $employeeId = $employeeTable->insert(new Employee(
            id: null,
            affiliateId: $affiliateId,
            firstName: 'Александр',
            lastName: 'Апакаев',
            middleName: 'Борисович',
            phone: '+79600961964',
            email: 'alexander.apakev@gmail.com',
            jobTitle: 'Программист',
            gender: GenderEnum::MALE,
            birthDate: new DateTimeImmutable('2005-01-29'),
            hireDate: new DateTimeImmutable('2022-09-19'),
            administratorComment: 'Студент Института iSpring',
            avatar: null,
        ));

        $employee = $employeeTable->findById($employeeId);
        $this->assertEmployee(
            $employee,
            affiliateId: $affiliateId,
            firstName: 'Александр',
            lastName: 'Апакаев',
            middleName: 'Борисович',
            phone: '+79600961964',
            email: 'alexander.apakev@gmail.com',
            jobTitle: 'Программист',
            gender: GenderEnum::MALE,
            birthDate: new DateTimeImmutable('2005-01-29'),
            hireDate: new DateTimeImmutable('2022-09-19'),
            administratorComment: 'Студент Института iSpring',
            avatar: null,
        );

        $employeeTable->update(new Employee(
            id: $employeeId,
            affiliateId: $affiliateId,
            firstName: 'Александр',
            lastName: 'Борисов',
            middleName: 'Кириллович',
            phone: '+79611961950',
            email: 'boris.apakev@gmail.com',
            jobTitle: 'Менеджер продаж',
            gender: GenderEnum::MALE,
            birthDate: new DateTimeImmutable('1978-01-29'),
            hireDate: new DateTimeImmutable('2018-09-19'),
            administratorComment: 'Сотрудник со стажем 5 лет',
            avatar: null,
        ));

        $employee = $employeeTable->findById($employeeId);
        $this->assertEmployee(
            $employee,
            affiliateId: $affiliateId,
            firstName: 'Александр',
            lastName: 'Борисов',
            middleName: 'Кириллович',
            phone: '+79611961950',
            email: 'boris.apakev@gmail.com',
            jobTitle: 'Менеджер продаж',
            gender: GenderEnum::MALE,
            birthDate: new DateTimeImmutable('1978-01-29'),
            hireDate: new DateTimeImmutable('2018-09-19'),
            administratorComment: 'Сотрудник со стажем 5 лет',
            avatar: null,
        );

        $employeeTable->delete($employee);

        $employee = $employeeTable->findById($employeeId);
        $this->assertNull($employee);
    }

    public function testFindByAffiliateId(): void
    {
        $employeeTable = $this->createEmployeeTable();
        $affiliateTable = $this->createAffiliateTable();
        $affiliateId = $affiliateTable->insert(new Affiliate(
            id: null,
            city: 'Йошкар-Ола',
            address: 'ул. Строителей, д.99',
            employeeCount: 10,
        ));
        $firstEmployeeId = $employeeTable->insert(new Employee(
            id: null,
            affiliateId: $affiliateId,
            firstName: 'Александр',
            lastName: 'Апакаев',
            middleName: 'Борисович',
            phone: '+79600961964',
            email: 'alexander.apakev@gmail.com',
            jobTitle: 'Программист',
            gender: GenderEnum::MALE,
            birthDate: new DateTimeImmutable('2005-01-29'),
            hireDate: new DateTimeImmutable('2022-09-19'),
            administratorComment: 'Студент Института iSpring',
            avatar: null,
        ));
        $secondEmployeeId = $employeeTable->insert(new Employee(
            id: null,
            affiliateId: $affiliateId,
            firstName: 'Александр',
            lastName: 'Борис',
            middleName: 'Кириллович',
            phone: '+79611961950',
            email: 'boris.apakev@gmail.com',
            jobTitle: 'Менеджер продаж',
            gender: GenderEnum::MALE,
            birthDate: new DateTimeImmutable('1978-01-29'),
            hireDate: new DateTimeImmutable('2018-09-19'),
            administratorComment: 'Сотрудник со стажем 5 лет',
            avatar: null,
        ));

        $employees = $employeeTable->findByAffiliateId($affiliateId);

        $this->assertEmployee(
            $employees[0],
            affiliateId: $affiliateId,
            firstName: 'Александр',
            lastName: 'Апакаев',
            middleName: 'Борисович',
            phone: '+79600961964',
            email: 'alexander.apakev@gmail.com',
            jobTitle: 'Программист',
            gender: GenderEnum::MALE,
            birthDate: new DateTimeImmutable('2005-01-29'),
            hireDate: new DateTimeImmutable('2022-09-19'),
            administratorComment: 'Студент Института iSpring',
            avatar: null,
        );
        $this->assertEmployee(
            $employees[1],
            affiliateId: $affiliateId,
            firstName: 'Александр',
            lastName: 'Борис',
            middleName: 'Кириллович',
            phone: '+79611961950',
            email: 'boris.apakev@gmail.com',
            jobTitle: 'Менеджер продаж',
            gender: GenderEnum::MALE,
            birthDate: new DateTimeImmutable('1978-01-29'),
            hireDate: new DateTimeImmutable('2018-09-19'),
            administratorComment: 'Сотрудник со стажем 5 лет',
            avatar: null,
        );
    }

    public function testSQLInjection(): void
    {
        $employeeTable = $this->createEmployeeTable();
        $affiliateTable = $this->createAffiliateTable();
        $affiliateId = $affiliateTable->insert(new Affiliate(
            id: null,
            city: 'Йошкар-Ола',
            address: 'ул. Строителей, д.99',
            employeeCount: 10,
        ));

        $employeeId = $employeeTable->insert(new Employee(
            id: null,
            affiliateId: $affiliateId,
            firstName: 'Александр\'; DROP TABLE employee;',
            lastName: 'Апакаев',
            middleName: 'Борисович',
            phone: '+79600961964',
            email: 'alexander.apakev@gmail.com',
            jobTitle: 'Программист',
            gender: GenderEnum::MALE,
            birthDate: new DateTimeImmutable('2005-01-29'),
            hireDate: new DateTimeImmutable('2022-09-19'),
            administratorComment: 'Студент Института iSpring',
            avatar: null,
        ));

        $employee = $employeeTable->findById($employeeId);
        $this->assertEmployee(
            $employee,
            affiliateId: $affiliateId,
            firstName: 'Александр\'; DROP TABLE employee;',
            lastName: 'Апакаев',
            middleName: 'Борисович',
            phone: '+79600961964',
            email: 'alexander.apakev@gmail.com',
            jobTitle: 'Программист',
            gender: GenderEnum::MALE,
            birthDate: new DateTimeImmutable('2005-01-29'),
            hireDate: new DateTimeImmutable('2022-09-19'),
            administratorComment: 'Студент Института iSpring',
            avatar: null,
        );
    }

    private function assertEmployee(
        Employee $actual,
        int $affiliateId,
        string $firstName,
        string $lastName,
        ?string $middleName,
        ?string $phone,
        string $email,
        string $jobTitle,
        GenderEnum $gender,
        DateTimeImmutable $birthDate,
        DateTimeImmutable $hireDate,
        ?string $administratorComment,
        ?string $avatar,
    ): void
    {
        $this->assertEquals($actual->getAffiliateId(), $affiliateId, 'employee -> affiliate city');
        $this->assertEquals($actual->getFirstName(), $firstName, 'employee -> firstname');
        $this->assertEquals($actual->getLastName(), $lastName, 'employee -> lastname');
        $this->assertEquals($actual->getMiddleName(), $middleName, 'employee -> middle name');
        $this->assertEquals($actual->getPhone(), $phone, 'employee -> phone number');
        $this->assertEquals($actual->getEmail(), $email, 'employee -> email');
        $this->assertEquals($actual->getJobTitle(), $jobTitle, 'employee -> job title');
        $this->assertEquals($actual->getGender()->value, $gender->value, 'employee -> gender');
        $this->assertEquals($actual->getBirthDate(), $birthDate, 'employee -> birthdate');
        $this->assertEquals($actual->getHireDate(), $hireDate, 'employee -> hire date');
        $this->assertEquals($actual->getAdministratorComment(), $administratorComment, 'employee -> comment');
        $this->assertEquals($actual->getAvatar(), $avatar, 'employee -> avatar');
    }

    private function createEmployeeTable(): EmployeeTable
    {
        $connection = $this->getConnection();
        return new EmployeeTable($connection);
    }

    private function createAffiliateTable(): AffiliateTable
    {
        $connection = $this->getConnection();
        return new AffiliateTable($connection);
    }
}