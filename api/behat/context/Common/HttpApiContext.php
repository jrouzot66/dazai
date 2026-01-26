<?php

namespace App\Behat\Common;

use Behat\Behat\Context\Context;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class HttpApiContext implements Context
{
    private ?\Symfony\Component\HttpFoundation\Response $response = null;

    public function __construct(
        private readonly KernelBrowser $client,
        private readonly TenantContext $tenantContext,
        private readonly JwtStorageContext $jwtStorage,
    ) {}

    public function request(string $method, string $path, ?array $jsonBody): void
    {
        $server = [
            'HTTP_HOST' => $this->tenantContext->getHost(),
            'CONTENT_TYPE' => 'application/json',
        ];

        $jwt = $this->jwtStorage->getJwt();
        if ($jwt) {
            $server['HTTP_AUTHORIZATION'] = 'Bearer ' . $jwt;
        }

        $content = $jsonBody !== null ? json_encode($jsonBody, JSON_THROW_ON_ERROR) : null;

        $this->client->request($method, $path, [], [], $server, $content);
        $this->response = $this->client->getResponse();
    }

    /**
     * @Then the response status should be :code
     */
    public function theResponseStatusShouldBe(int $code): void
    {
        if (!$this->response) {
            throw new \RuntimeException('No response available.');
        }

        $actual = $this->response->getStatusCode();
        if ($actual !== $code) {
            throw new \RuntimeException(sprintf(
                'Expected status %d, got %d. Body: %s',
                $code,
                $actual,
                (string) $this->response->getContent()
            ));
        }
    }

    public function getJson(): array
    {
        if (!$this->response) {
            throw new \RuntimeException('No response available.');
        }

        $content = (string) $this->response->getContent();
        $data = json_decode($content, true);

        if (!is_array($data)) {
            throw new \RuntimeException('Response is not JSON: ' . $content);
        }

        return $data;
    }
}