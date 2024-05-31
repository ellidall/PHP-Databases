<?php
declare(strict_types=1);

namespace App\Tests\Common;

use App\Common\Database\Connection;
use App\Common\Database\ConnectionProvider;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\BrowserKit\AbstractBrowser;
use Symfony\Component\BrowserKit\Response;

abstract class AbstractDatabaseTestCase extends WebTestCase
{
    private Connection $connection;
    private AbstractBrowser $client;

    protected function setUp(): void
    {
        parent::setUp();
        $this->client = static::createClient();
        $this->connection = ConnectionProvider::getConnection();
        $this->connection->beginTransaction();
    }

    protected function tearDown(): void
    {
        $this->connection->rollback();
        parent::tearDown();
        $this->restoreExceptionHandler();
    }

    final protected function getConnection(): Connection
    {
        return $this->connection;
    }

    private function restoreExceptionHandler(): void
    {
        while (true) {
            $previousHandler = set_exception_handler(static fn() => null);

            restore_exception_handler();

            if ($previousHandler === null) {
                break;
            }

            restore_exception_handler();
        }
    }

    protected function doCreateAffiliate(string $city, string $address): int
    {
        $response = $this->sendPostRequest(
            '/affiliate/add',
            [
                'city' => $city,
                'address' => $address,
            ]
        );

        $this->assertStatusCode(302, $response);

        $this->assertResponseRedirects();
        $redirectUrl = $response->getHeaders()['location'][0];

        $this->assertMatchesRegularExpression('/^\/affiliate\/\d+$/', $redirectUrl, 'Redirect URL does not match the expected pattern.');
        preg_match('/^\/affiliate\/(\d+)$/', $redirectUrl, $matches);

        $affiliateId = $matches[1];
        $this->assertIsNumeric($affiliateId, 'Affiliate ID should be numeric');

        return (int)$affiliateId;
    }

    protected function assertStatusCode(int $statusCode, Response $response): void
    {
        $this->assertEquals($statusCode, $response->getStatusCode(), "status code must be $statusCode");
    }

    /**
     * @param string $urlPath
     * @param array $requestParams
     * @return Response
     */
    protected function sendPostRequest(string $urlPath, array $requestParams): Response
    {
        return $this->doRequest('POST', $urlPath, $requestParams);
    }

    /**
     * @param string $urlPath
     * @param array $queryParams
     * @return Response
     */
    protected function sendGetRequest(string $urlPath, array $queryParams): Response
    {
        $urlString = $urlPath . '?' . http_build_query($queryParams);
        return $this->doRequest('GET', $urlString);
    }

    private function doRequest(string $method, string $url, array $body = []): Response
    {
        $this->client->request($method, $url, $body);
        return $this->client->getInternalResponse();
    }
}