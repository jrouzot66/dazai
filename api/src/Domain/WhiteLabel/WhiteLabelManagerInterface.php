<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Domain\WhiteLabel;

use App\Domain\WhiteLabel\Dto\WhiteLabelInput;
use App\Entity\WhiteLabel;

interface WhiteLabelManagerInterface
{
    public function create(WhiteLabelInput $input): WhiteLabel;
}