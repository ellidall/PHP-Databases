<?php
declare(strict_types=1);

namespace App\Tests\Functional;

use App\Tests\Common\AbstractDatabaseTestCase;
use Symfony\Component\BrowserKit\Response;

class AffiliateControllerTest extends AbstractDatabaseTestCase
{
    public function testCreateEditAndDeleteDepartment(): void
    {
        $affiliateId = $this->doCreateAffiliate(
            city: 'Йошкар-Ола',
            address: 'ул.Волкова, д.108'
        );

        $response = $this->doGetAffiliatePage($affiliateId, 200);
        $responseContent = $response->getContent();
        $this->assertStringContainsString('Йошкар-Ола, ул.Волкова, д.108', $responseContent);

        $this->doEditAffiliate(
            affiliateId: $affiliateId,
            city: 'Чебоксары',
            address: 'ул.Ломоносова'
        );
        $response = $this->doGetAffiliatePage($affiliateId, 200);
        $responseContent = $response->getContent();

        $this->assertStringContainsString('Чебоксары, ул.Ломоносова', $responseContent);

        $this->doDeleteAffiliate($affiliateId);
        //TODO: проверить возвращаемые данные
        $this->doGetAffiliateListPage(200);
    }

    private function doGetAffiliatePage(int $affiliateId, int $statusCode): Response
    {
        $response = $this->sendGetRequest('/affiliate/' . $affiliateId, []);
        $this->assertStatusCode($statusCode, $response);

        return $response;
    }

    private function doGetAffiliateListPage(int $statusCode): Response
    {
        $response = $this->sendGetRequest('/', []);
        $this->assertStatusCode($statusCode, $response);

        return $response;
    }

    private function doEditAffiliate(int $affiliateId, string $city, string $address): void
    {
        $response = $this->sendPostRequest(
            '/affiliate/update',
            [
                'id' => (string)$affiliateId,
                'city' => $city,
                'address' => $address,
            ]
        );

        $this->assertStatusCode(302, $response);
        $this->assertResponseRedirects();
    }

    private function doDeleteAffiliate(int $affiliateId): void
    {
        $response = $this->sendPostRequest(
            '/affiliate/delete',
            ['id' => $affiliateId]
        );

        $this->assertStatusCode(200, $response);
    }
}