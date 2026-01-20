<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Domain\WhiteLabel;

use App\Domain\WhiteLabel\Dto\WhiteLabelInput;
use App\Entity\WhiteLabel;
use Doctrine\ORM\EntityManagerInterface;

class WhiteLabelManager implements WhiteLabelManagerInterface
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager
    ) {}

    public function create(WhiteLabelInput $input): WhiteLabel
    {
        $whiteLabel = new WhiteLabel();
        $whiteLabel->setName($input->name);
        $whiteLabel->setDomainUrl($input->domainUrl);
        $whiteLabel->setConfig($input->config);

        $this->entityManager->persist($whiteLabel);
        $this->entityManager->flush();

        return $whiteLabel;
    }
}