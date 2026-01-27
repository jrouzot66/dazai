<?php

namespace App\Behat\Delivery;

use App\Behat\Common\HttpApiContext;
use App\Behat\Common\SeedContext;
use Behat\Behat\Context\Context;
use PHPUnit\Framework\Assert;

class DeliveryWorkflowContext implements Context
{
    private ?int $deliveryId = null;

    public function __construct(
        private readonly SeedContext $seed,
        private readonly HttpApiContext $http,
    ) {}

    /**
     * @When I create a delivery as MO
     */
    public function iCreateADeliveryAsMo(): void
    {
        $this->http->request('POST', '/api/mo/deliveries', [
            'pickupAddress' => '10 rue de Paris',
            'dropoffAddress' => '20 avenue de Lyon',
            'vendorId' => $this->seed->getVendorOrgId(),
            'buyerId' => $this->seed->getBuyerOrgId(),
        ]);

        $this->http->theResponseStatusShouldBe(201);

        $data = $this->http->getJson();
        $this->deliveryId = isset($data['id']) ? (int) $data['id'] : null;

        Assert::assertNotNull($this->deliveryId, 'Delivery id missing after creation.');
    }

    /**
     * @When I create a delivery as MO with invalid payload
     */
    public function iCreateADeliveryAsMoWithInvalidPayload(): void
    {
        $this->http->request('POST', '/api/mo/deliveries', [
            'pickupAddress' => '',
            'dropoffAddress' => '',
            'vendorId' => 0,
            'buyerId' => 0,
        ]);
    }

    /**
     * @When I plan the delivery
     */
    public function iPlanTheDelivery(): void
    {
        Assert::assertNotNull($this->deliveryId, 'No deliveryId stored.');

        $this->http->request('POST', '/api/mo/deliveries/' . $this->deliveryId . '/transitions/plan', null);
        $this->http->theResponseStatusShouldBe(200);
    }

    /**
     * @When I plan the delivery again
     */
    public function iPlanTheDeliveryAgain(): void
    {
        Assert::assertNotNull($this->deliveryId, 'No deliveryId stored.');

        $this->http->request('POST', '/api/mo/deliveries/' . $this->deliveryId . '/transitions/plan', null);
    }
}