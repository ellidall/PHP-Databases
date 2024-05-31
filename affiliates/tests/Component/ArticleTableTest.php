<?php
declare(strict_types=1);

namespace App\Tests\Component;

use App\Database\AffiliateTable;
use App\Model\Affiliate;
use App\Tests\Common\AbstractDatabaseTestCase;

class ArticleTableTest extends AbstractDatabaseTestCase
{
    public function testCreateEditAndDeleteAffiliate(): void
    {
        $table = $this->createAffiliateTable();

        $affiliateId = $table->insert(new Affiliate(
            id: null,
            city: 'Йошкар-Ола',
            address: 'ул. Строителей, д.99',
        ));

        $affiliate = $table->findById($affiliateId);

        $this->assertAffiliate(
            $affiliate,
            city: 'Йошкар-Ола',
            address: 'ул. Строителей, д.99',
        );

        $table->update(new Affiliate(
            id: $affiliateId,
            city: 'Чебоксары',
            address: 'ул. Строителей, д.110',
        ));

        $affiliate = $table->findById($affiliateId);
        $this->assertAffiliate(
            $affiliate,
            city: 'Чебоксары',
            address: 'ул. Строителей, д.110',
        );

        $table->delete($affiliate);

        $affiliate = $table->findById($affiliateId);
        $this->assertNull($affiliate);
    }

    public function testListAllAffiliates(): void
    {
        $table = $this->createAffiliateTable();
        $firstAffiliateId = $table->insert(new Affiliate(
            id: null,
            city: 'Йошкар-Ола',
            address: 'ул. Строителей, д.99',
        ));
        $secondAffiliateId = $table->insert(new Affiliate(
            id: null,
            city: 'Чебоксары',
            address: 'ул. Ломоносова, д.2a',
        ));

        $affiliates = $table->listAll();

        $this->assertAffiliate(
            $affiliates[0],
            city: 'Йошкар-Ола',
            address: 'ул. Строителей, д.99',
        );
        $this->assertAffiliate(
            $affiliates[1],
            city: 'Чебоксары',
            address: 'ул. Ломоносова, д.2a',
        );
    }

    public function testSQLInjection(): void
    {
        $table = $this->createAffiliateTable();
        $affiliateId = $table->insert(new Affiliate(
            id: null,
            city: 'Йошкар-Ола\'; DROP TABLE affiliate;',
            address: 'ул. Строителей, д.99',
        ));

        $affiliate = $table->findById($affiliateId);

        $this->assertAffiliate(
            $affiliate,
            city: 'Йошкар-Ола\'; DROP TABLE affiliate;',
            address: 'ул. Строителей, д.99',
        );
    }

    private function assertAffiliate(
        Affiliate $actual,
        string $city = '',
        string $address = '',
        int $employeeCount = 0,
    ): void
    {
        $this->assertEquals($actual->getCity(), $city, 'affiliate city');
        $this->assertEquals($actual->getAddress(), $address, 'affiliate address');
        $this->assertEquals($actual->getEmployeeCount(), $employeeCount, 'employee count');
    }

    private function createAffiliateTable(): AffiliateTable
    {
        $connection = $this->getConnection();
        return new AffiliateTable($connection);
    }
}