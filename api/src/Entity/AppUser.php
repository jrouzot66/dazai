<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Entity;

use App\Domain\MultiTenant\TenantAwareInterface;
use App\Domain\MultiTenant\TenantAwareTrait;
use App\Repository\AppUserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Context\ExecutionContextInterface;

#[ORM\Entity(repositoryClass: AppUserRepository::class)]
#[ORM\Table(name: 'app_user')]
class AppUser implements UserInterface, PasswordAuthenticatedUserInterface, TenantAwareInterface
{
    use TenantAwareTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\Column]
    private ?string $password = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: true)]
    private ?Organization $organization = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $azureId = null;

    private ?string $plainPassword = null;

    public function getId(): ?int { return $this->id; }
    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): self { $this->email = $email; return $this; }
    public function getUserIdentifier(): string { return (string) $this->email; }
    public function getRoles(): array {
        $roles = $this->roles;
        $roles[] = 'ROLE_USER';
        return array_unique($roles);
    }
    public function setRoles(array $roles): self { $this->roles = $roles; return $this; }
    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): self { $this->password = $password; return $this; }
    public function eraseCredentials(): void {}

    public function getOrganization(): ?Organization { return $this->organization; }
    public function setOrganization(?Organization $organization): self { $this->organization = $organization; return $this; }
    public function getAzureId(): ?string { return $this->azureId; }
    public function setAzureId(?string $azureId): self { $this->azureId = $azureId; return $this; }

    /**
     * Validation personnalisée pour garantir la cohérence Multi-tenant
     */
    #[Assert\Callback]
    public function validateConsistency(ExecutionContextInterface $context): void
    {
        // Si l'utilisateur est rattaché à une organisation
        if ($this->getOrganization() !== null) {

            // On vérifie que la marque blanche de l'organisation match celle de l'utilisateur
            if ($this->getOrganization()->getWhiteLabel() !== $this->getWhiteLabel()) {
                $context->buildViolation('L\'organisation sélectionnée doit appartenir à la Marque Blanche choisie.')
                    ->atPath('organization')
                    ->addViolation();
            }
        }

        // Sécurité supplémentaire : un utilisateur FO doit avoir une organisation
        if (in_array('ROLE_FO_VENDOR', $this->roles) || in_array('ROLE_FO_BUYER', $this->roles)) {
            if ($this->getOrganization() === null) {
                $context->buildViolation('Un utilisateur de type Front-Office doit obligatoirement être lié à une organisation.')
                    ->atPath('organization')
                    ->addViolation();
            }
        }
    }

    public function getPlainPassword(): ?string { return $this->plainPassword; }

    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        // Important : on vide le password actuel pour forcer Doctrine à voir un changement
        if ($plainPassword) { $this->password = null; }

        return $this;
    }
}