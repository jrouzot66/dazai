<?php


/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Entity;

use App\Entity\Enum\OrganizationType;
use App\Repository\OrganizationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrganizationRepository::class)]
#[ORM\Table(name: 'organization')]
class Organization
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $name = null;

    #[ORM\Column(length: 20, enumType: OrganizationType::class)]
    private ?OrganizationType $type = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?WhiteLabel $whiteLabel = null;

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

    public function getType(): ?OrganizationType
    {
        return $this->type;
    }

    public function setType(OrganizationType $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getWhiteLabel(): ?WhiteLabel
    {
        return $this->whiteLabel;
    }

    public function setWhiteLabel(?WhiteLabel $whiteLabel): self
    {
        $this->whiteLabel = $whiteLabel;
        return $this;
    }

    public function __toString(): string
    {
        return $this->name ?? 'Nouvelle Organisation';
    }
}