<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Entity;

use App\Repository\WhiteLabelRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WhiteLabelRepository::class)]
#[ORM\Table(name: 'white_label')]
class WhiteLabel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 255, unique: true)]
    private ?string $domainUrl = null;

    #[ORM\Column(type: 'json')]
    private array $config = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getDomainUrl(): ?string
    {
        return $this->domainUrl;
    }

    public function setDomainUrl(string $domainUrl): self
    {
        $this->domainUrl = $domainUrl;
        return $this;
    }

    public function getConfig(): array
    {
        return $this->config;
    }

    public function setConfig(array $config): self
    {
        $this->config = $config;
        return $this;
    }

    public function getConfigString(): string
    {
        return json_encode($this->config, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) ?: '{}';
    }

    public function setConfigString(string $config): self
    {
        $this->config = json_decode($config, true) ?: [];
        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? 'Nouvelle Marque Blanche';
    }
}