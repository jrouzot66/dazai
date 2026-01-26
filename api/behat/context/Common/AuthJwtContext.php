<?php

namespace App\Behat\Common;

use Behat\Behat\Context\Context;

class AuthJwtContext implements Context
{
    public function __construct(
        private readonly HttpApiContext $http,
        private readonly JwtStorageContext $jwtStorage
    ) {}

    /**
     * @When I login as :email with password :password
     */
    public function iLoginAsWithPassword(string $email, string $password): void
    {
        $this->http->request('POST', '/api/login', [
            'email' => $email,
            'password' => $password,
        ]);

        $this->http->theResponseStatusShouldBe(200);

        $data = $this->http->getJson();
        $jwt = $data['token'] ?? null;

        if (!$jwt) {
            throw new \RuntimeException('JWT token missing in response.');
        }

        $this->jwtStorage->setJwt($jwt);
    }
}