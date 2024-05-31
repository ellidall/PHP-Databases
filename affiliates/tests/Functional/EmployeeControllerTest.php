<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use App\Common\GenderEnum;
use App\Tests\Common\AbstractDatabaseTestCase;
use Symfony\Component\BrowserKit\Response;

class EmployeeControllerTest extends AbstractDatabaseTestCase
{
    //TODO: вынести в класс метод doCreateAffiliate
    public function testCreateEditAndDeleteEmployee(): void
    {
        $affiliateId = $this->doCreateAffiliate(
            city: 'Йошкар-Ола',
            address: 'Вознесенская, 110'
        );

        $employeeId = $this->doCreateEmployee(
            affiliateId: $affiliateId,
            firstName: 'Александр',
            lastName: 'Апакаев',
            middleName: 'Борисович',
            jobTitle: 'Программист',
            phone: '+79379337060',
            email: 'alexander.apakaev@mail.ru',
            gender: GenderEnum::MALE->value,
            birthDate: '2022-09-19',
            hireDate: '2024-03-01',
            comment: 'Студент института iSpring'
        );

        $response = $this->doGetEmployeePage($employeeId, 200);
        $responseContent = $response->getContent();
        $this->assertEmployeeData(
            $responseContent,
            'Александр',
            'Апакаев',
            'Борисович',
            'Программист',
            '+79379337060',
            'alexander.apakaev@mail.ru',
            'Мужской',
            '2022-09-19',
            '2022-09-19',
            'Студент института iSpring'
        );

        $this->doEditEmployee(
            id: $employeeId,
            affiliateId: $affiliateId,
            firstName: 'Борис',
            lastName: 'Апакаев',
            middleName: 'Валерьянович',
            jobTitle: 'Менеджер',
            phone: '+79379337060',
            email: 'alexander.apakaev@mail.ru',
            gender: GenderEnum::MALE->value,
            birthDate: '2022-09-19',
            hireDate: '2024-03-01',
            comment: 'Харош'
        );
        $response = $this->doGetEmployeePage($employeeId, 200);
        $responseContent = $response->getContent();
        $this->assertEmployeeData(
            $responseContent,
            'Борис',
            'Апакаев',
            'Валерьянович',
            'Менеджер',
            '+79379337060',
            'alexander.apakaev@mail.ru',
            'Мужской',
            '2022-09-19',
            '2022-09-19',
            'Харош'
        );

        $this->doDeleteEmployee($employeeId);
        $this->doGetEmployeePage($employeeId, 404);
    }

    private function doCreateEmployee(
        int $affiliateId,
        string $firstName,
        string $lastName,
        string $middleName,
        string $jobTitle,
        string $phone,
        string $email,
        int $gender,
        string $birthDate,
        string $hireDate,
        string $comment
    ): int
    {
        $response = $this->sendPostRequest(
            '/employee/add',
            [
                'affiliateId' => $affiliateId,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'middleName' => $middleName,
                'jobTitle' => $jobTitle,
                'phone' => $phone,
                'email' => $email,
                'gender' => $gender,
                'birthDate' => $birthDate,
                'hireDate' => $hireDate,
                'comment' => $comment,
            ]
        );
        $this->assertStatusCode(302, $response);

        $this->assertResponseRedirects();
        $redirectUrl = $response->getHeaders()['location'][0];

        $this->assertMatchesRegularExpression('/^\/employee\/\d+$/', $redirectUrl, 'Redirect URL does not match the expected pattern.');

        preg_match('/^\/employee\/(\d+)$/', $redirectUrl, $matches);
        $employeeId = $matches[1];
        $this->assertIsNumeric($employeeId, 'Employee ID should be numeric');

        return (int)$employeeId;
    }

    private function doGetEmployeePage(int $employeeId, int $statusCode): Response
    {
        $response = $this->sendGetRequest("/employee/{$employeeId}", []);
        $this->assertStatusCode($statusCode, $response);

        return $response;
    }

    private function doEditEmployee(
        int $id,
        int $affiliateId,
        string $firstName,
        string $lastName,
        string $middleName,
        string $jobTitle,
        string $phone,
        string $email,
        int $gender,
        string $birthDate,
        string $hireDate,
        string $comment
    ): void
    {
        $response = $this->sendPostRequest(
            '/employee/update',
            [
                'id' => $id,
                'affiliateId' => $affiliateId,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'middleName' => $middleName,
                'jobTitle' => $jobTitle,
                'phone' => $phone,
                'email' => $email,
                'gender' => $gender,
                'birthDate' => $birthDate,
                'hireDate' => $hireDate,
                'comment' => $comment,
            ]
        );

        $this->assertStatusCode(302, $response);
    }

    private function doDeleteEmployee(int $employeeId): void
    {
        $response = $this->sendPostRequest(
            '/employee/delete',
            ['id' => $employeeId]
        );

        $this->assertStatusCode(302, $response);
        $this->assertResponseRedirects();
    }

    private function assertEmployeeData(
        string $content,
        string $firstName,
        string $lastName,
        string $middleName,
        string $jobTitle,
        string $phone,
        string $email,
        string $gender,
        string $birthDate,
        string $hireDate,
        string $comment
    )
    {
        $this->assertStringContainsString($firstName, $content);
        $this->assertStringContainsString($lastName, $content);
        $this->assertStringContainsString($middleName, $content);
        $this->assertStringContainsString($jobTitle, $content);
        $this->assertStringContainsString($phone, $content);
        $this->assertStringContainsString($email, $content);
        $this->assertStringContainsString($gender, $content);
        $this->assertStringContainsString($birthDate, $content);
        $this->assertStringContainsString($hireDate, $content);
        $this->assertStringContainsString($comment, $content);
    }
}