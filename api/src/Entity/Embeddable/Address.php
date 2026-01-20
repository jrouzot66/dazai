<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Entity\Embeddable;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class Address
{
    public function __construct(
        #[ORM\Column(length: 255)]
        private ?string $street = null,

        #[ORM\Column(length: 100)]
        private ?string $city = null,

        #[ORM\Column(length: 10)]
        private ?string $zipCode = null,

        #[ORM\Column(length: 10)]
        private ?string $countryIsoCode = null
    ) {
    }

    public function getStreet(): ?string {
        return $this->street;
    }

    public function setStreet(string $street): void {
        $this->street = $street;
    }

    public function getCity(): ?string {
        return $this->city;
    }

    public function setCity(string $city): void {
        $this->city = $city;
    }

    public function getZipCode(): ?string {
        return $this->zipCode;
    }

    public function setZipCode(string $zipCode): void {
        $this->zipCode = $zipCode;
    }

    public function getCountryIsoCode(): ?string {
        return $this->countryIsoCode;
    }

    public function setCountryIsoCode(string $countryIsoCode): void {
        $this->countryIsoCode = $countryIsoCode;
    }
}