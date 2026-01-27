<?php

namespace App\Behat\Common;

use App\Entity\AppUser;
use App\Entity\Enum\OrganizationType;
use App\Entity\Organization;
use App\Entity\WhiteLabel;
use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class SeedContext implements Context
{
    private int $vendorOrgId;
    private int $buyerOrgId;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserPasswordHasherInterface $hasher,
        private readonly TenantContext $tenantContext,
        private readonly JwtStorageContext $jwtStorage,
    ) {}

    /** @BeforeScenario */
    public function beforeScenario(BeforeScenarioScope $scope): void
    {
        $this->em->getConnection()->beginTransaction();
        $this->jwtStorage->clear();

        $this->seedTenantOrgsUsers();
    }

    /** @AfterScenario */
    public function afterScenario(AfterScenarioScope $scope): void
    {
        $this->em->getConnection()->rollBack();
        $this->em->clear();
    }

    public function getVendorOrgId(): int
    {
        return $this->vendorOrgId;
    }

    public function getBuyerOrgId(): int
    {
        return $this->buyerOrgId;
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
        $vendor->setType(OrganizationType::VENDOR);
        $this->em->persist($vendor);

        $buyer = new Organization();
        $buyer->setName('Buyer Org');
        $buyer->setWhiteLabel($tenant);
        $buyer->setType(OrganizationType::BUYER);
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
}