<?php

namespace App\Behat\Common;

use Behat\Behat\Context\Context;

class JwtStorageContext implements Context
{
    private ?string $jwt = null;

    public function setJwt(?string $jwt): void
    {
        $this->jwt = $jwt;
    }

    public function getJwt(): ?string
    {
        return $this->jwt;
    }

    public function clear(): void
    {
        $this->jwt = null;
    }
}