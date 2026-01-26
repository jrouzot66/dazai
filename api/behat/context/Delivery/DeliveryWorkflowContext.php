<?php

namespace App\Behat\Delivery;

use App\Entity\AppUser;
use App\Entity\Organization;
use App\Entity\WhiteLabel;
use App\Behat\Common\AuthJwtContext;
use App\Behat\Common\HttpApiContext;
use App\Behat\Common\JwtStorageContext;
use App\Behat\Common\TenantContext;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class DeliveryWorkflowContext implements Context
{
    private int $vendorOrgId;
    private int $buyerOrgId;
    private ?int $deliveryId = null;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly TenantContext $tenantContext,
        private readonly JwtStorageContext $jwtStorage,
        private readonly HttpApiContext $http,
    ) {}

    /** @BeforeScenario */
    public function beforeScenario(BeforeScenarioScope $scope): void
    {
        $this->em->getConnection()->beginTransaction();
        $this->jwtStorage->clear();

        $this->seedTenantOrgsUsers();
        $this->deliveryId = null;
    }

    /** @AfterScenario */
    public function afterScenario(AfterScenarioScope $scope): void
    {
        $this->em->getConnection()->rollBack();
        $this->em->clear();
    }

    private function seedTenantOrgsUsers(): void
    {
        $host = $this->tenantContext->getHost();

        $tenant = new WhiteLabel();
        $tenant->setName('Tenant Test');
        $tenant->setDomainUrl($host);
        $tenant->setConfig([]);
        $this->em->persist($tenant);

        $vendor = new Organization();
        $vendor->setName('Vendor Org');
        $vendor->setWhiteLabel($tenant);
        $this->em->persist($vendor);

        $buyer = new Organization();
        $buyer->setName('Buyer Org');
        $buyer->setWhiteLabel($tenant);
        $this->em->persist($buyer);

        $mo = new AppUser();
        $mo->setEmail('mo@example.test');
        $mo->setRoles(['ROLE_MO_MANAGER']);
        $mo->setWhiteLabel($tenant);
        $mo->setPassword($this->hasher->hashPassword($mo, 'password'));
        $this->em->persist($mo);

        $foVendor = new AppUser();
        $foVendor->setEmail('vendor@example.test');
        $foVendor->setRoles(['ROLE_FO_VENDOR']);
        $foVendor->setWhiteLabel($tenant);
        $foVendor->setOrganization($vendor);
        $foVendor->setPassword($this->hasher->hashPassword($foVendor, 'password'));
        $this->em->persist($foVendor);

        $foBuyer = new AppUser();
        $foBuyer->setEmail('buyer@example.test');
        $foBuyer->setRoles(['ROLE_FO_BUYER']);
        $foBuyer->setWhiteLabel($tenant);
        $foBuyer->setOrganization($buyer);
        $foBuyer->setPassword($this->hasher->hashPassword($foBuyer, 'password'));
        $this->em->persist($foBuyer);

        $this->em->flush();

        $this->vendorOrgId = (int) $vendor->getId();
        $this->buyerOrgId = (int) $buyer->getId();
    }

    /**
     * @When I create a delivery as MO
     */
    public function iCreateADeliveryAsMo(): void
    {
        $this->http->request('POST', '/api/mo/deliveries', [
            'pickupAddress' => '10 rue de Paris',
            'dropoffAddress' => '20 avenue de Lyon',
            'vendorId' => $this->vendorOrgId,
            'buyerId' => $this->buyerOrgId,
        ]);

        $this->http->theResponseStatusShouldBe(201);

        $data = $this->http->getJson();
        $this->deliveryId = $data['id'] ?? null;

        if (!$this->deliveryId) {
            throw new \RuntimeException('Delivery id missing after creation.');
        }
    }

    /**
     * @When I plan the delivery
     */
    public function iPlanTheDelivery(): void
    {
        if (!$this->deliveryId) {
            throw new \RuntimeException('No deliveryId stored.');
        }

        $this->http->request('POST', '/api/mo/deliveries/' . $this->deliveryId . '/transitions/plan', null);
        $this->http->theResponseStatusShouldBe(200);
    }
}