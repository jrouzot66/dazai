<?php

/**
 * @Developer Rouzot Julien copyright 2026 Agence Webnet.fr
 */

namespace App\Domain\WhiteLabel\Dto;

class WhiteLabelInput
{
    public function __construct(
        public string $name,
        public string $domainUrl,
        public array $config = []
    ) {}
}