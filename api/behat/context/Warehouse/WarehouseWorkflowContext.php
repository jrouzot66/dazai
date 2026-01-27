<?php

namespace App\Behat\Warehouse;

use App\Behat\Common\HttpApiContext;
use Behat\Behat\Context\Context;

class WarehouseWorkflowContext implements Context
{
    public function __construct(
        private readonly HttpApiContext $http,
    ) {}

    /**
     * @When I create a warehouse as MO
     */
    public function iCreateAWarehouseAsMo(): void
    {
        $this->http->request('POST', '/api/mo/warehouses', [
            'name' => 'Main Warehouse',
            'address' => '1 rue de la Logistique',
        ]);
    }

    /**
     * @When I list warehouses as MO
     */
    public function iListWarehousesAsMo(): void
    {
        $this->http->request('GET', '/api/mo/warehouses', null);
    }
}