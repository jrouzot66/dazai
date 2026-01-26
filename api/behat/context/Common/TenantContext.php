<?php

namespace App\Behat\Common;

use Behat\Behat\Context\Context;

class TenantContext implements Context
{
    private string $host = 'tornigie.fluxion.local';

    /**
     * @Given I am on tenant :host
     */
    public function iAmOnTenant(string $host): void
    {
        $this->host = $host;
    }

    public function getHost(): string
    {
        return $this->host;
    }
}