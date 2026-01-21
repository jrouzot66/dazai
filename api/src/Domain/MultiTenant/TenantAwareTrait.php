<?php


/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Domain\MultiTenant;

use App\Entity\WhiteLabel;
use Doctrine\ORM\Mapping as ORM;

trait TenantAwareTrait
{
    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?WhiteLabel $whiteLabel = null;

    public function getWhiteLabel(): ?WhiteLabel
    {
        return $this->whiteLabel;
    }

    public function setWhiteLabel(?WhiteLabel $whiteLabel): self
    {
        $this->whiteLabel = $whiteLabel;

        return $this;
    }
}