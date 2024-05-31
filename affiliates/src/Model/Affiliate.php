<?php
declare(strict_types = 1);

namespace App\Model;

class Affiliate
{
    private ?int $id;
    private string $city;
    private string $address;
    private int $employeeCount;

    public function __construct(
        ?int $id,
        string $city,
        string $address,
        int $employeeCount = 0,
    )
    {
        $this->id = $id;
        $this->city = $city;
        $this->address = $address;
        $this->employeeCount = $employeeCount;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCity(): string
    {
        return $this->city;
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getEmployeeCount(): int
    {
        return $this->employeeCount;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function setCity(string $city): void
    {
        $this->city = $city;
    }

    public function setAddress(string $address): void
    {
        $this->address = $address;
    }

    public function setEmployeeCount(int $employeeCount): void
    {
        $this->employeeCount = $employeeCount;
    }
}