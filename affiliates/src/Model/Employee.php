<?php
declare(strict_types = 1);

namespace App\Model;

use App\Common\GenderEnum;
use DateTimeImmutable;

class Employee
{
    private ?int $id;
    private int $affiliateId;
    private string $firstName;
    private string $lastName;
    private ?string $middleName;
    private ?string $phone;
    private string $email;
    private string $jobTitle;
    private GenderEnum $gender;
    private DateTimeImmutable $birthDate;
    private DateTimeImmutable $hireDate;
    private ?string $administratorComment;

    private ?string $avatar;

    public function __construct(
        ?int $id,
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
    )
    {
        $this->id = $id;
        $this->affiliateId = $affiliateId;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->middleName = $middleName;
        $this->phone = $phone;
        $this->email = $email;
        $this->jobTitle = $jobTitle;
        $this->gender = $gender;
        $this->birthDate = $birthDate;
        $this->hireDate = $hireDate;
        $this->administratorComment = $administratorComment;
        $this->avatar = $avatar;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAffiliateId(): int
    {
        return $this->affiliateId;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function getMiddleName(): ?string
    {
        return $this->middleName;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getJobTitle(): string
    {
        return $this->jobTitle;
    }

    public function getGender(): GenderEnum
    {
        return $this->gender;
    }

    public function getBirthDate(): DateTimeImmutable
    {
        return $this->birthDate;
    }

    public function getHireDate(): DateTimeImmutable
    {
        return $this->hireDate;
    }

    public function getAdministratorComment(): ?string
    {
        return $this->administratorComment;
    }

    public function getAvatar(): ?string
    {
        return $this->avatar;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }
}