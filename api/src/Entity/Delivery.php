<?php

namespace App\Entity;

use App\Domain\MultiTenant\TenantAwareInterface;
use App\Domain\MultiTenant\TenantAwareTrait;
use App\Entity\Enum\DeliveryStatus;
use App\Repository\DeliveryRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Ulid;

#[ORM\Entity(repositoryClass: DeliveryRepository::class)]
#[ORM\Table(
    name: 'delivery',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'uniq_delivery_tenant_reference', columns: ['white_label_id', 'reference'])
    ]
)]
class Delivery implements TenantAwareInterface
{
    use TenantAwareTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private ?string $reference = null;

    /**
     * IMPORTANT (Workflow):
     * Le workflow va lire/écrire ici. On stocke une string.
     * On garde l'enum DeliveryStatus comme "contrat" métier via les getters/setters ci-dessous.
     */
    #[ORM\Column(length: 32)]
    private string $status = DeliveryStatus::DRAFT->value;

    #[ORM\Column(length: 255)]
    private ?string $pickupAddress = null;

    #[ORM\Column(length: 255)]
    private ?string $dropoffAddress = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $plannedAt = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Organization $vendor = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?Organization $buyer = null;

    #[ORM\Column]
    private \DateTimeImmutable $createdAt;

    #[ORM\Column]
    private \DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $now = new \DateTimeImmutable();
        $this->createdAt = $now;
        $this->updatedAt = $now;

        $this->reference = 'DLV-' . (new Ulid())->toBase32();
    }

    public function touch(): void
    {
        $this->updatedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getReference(): ?string { return $this->reference; }
    public function setReference(string $reference): self { $this->reference = $reference; return $this; }

    /**
     * Le workflow utilise getStatus()/setStatus() via marking_store "method".
     * Donc ces méthodes doivent manipuler une string.
     */
    public function getStatus(): string { return $this->status; }
    public function setStatus(string $status): self { $this->status = $status; return $this; }

    /**
     * Helpers "métier" optionnels: on expose aussi l'enum.
     */
    public function getStatusEnum(): DeliveryStatus
    {
        return DeliveryStatus::from($this->status);
    }

    public function setStatusEnum(DeliveryStatus $status): self
    {
        $this->status = $status->value;
        return $this;
    }

    public function getPickupAddress(): ?string { return $this->pickupAddress; }
    public function setPickupAddress(string $pickupAddress): self { $this->pickupAddress = $pickupAddress; return $this; }

    public function getDropoffAddress(): ?string { return $this->dropoffAddress; }
    public function setDropoffAddress(string $dropoffAddress): self { $this->dropoffAddress = $dropoffAddress; return $this; }

    public function getPlannedAt(): ?\DateTimeImmutable { return $this->plannedAt; }
    public function setPlannedAt(?\DateTimeImmutable $plannedAt): self { $this->plannedAt = $plannedAt; return $this; }

    public function getVendor(): ?Organization { return $this->vendor; }
    public function setVendor(Organization $vendor): self { $this->vendor = $vendor; return $this; }

    public function getBuyer(): ?Organization { return $this->buyer; }
    public function setBuyer(Organization $buyer): self { $this->buyer = $buyer; return $this; }

    public function getCreatedAt(): \DateTimeImmutable { return $this->createdAt; }
    public function getUpdatedAt(): \DateTimeImmutable { return $this->updatedAt; }
}