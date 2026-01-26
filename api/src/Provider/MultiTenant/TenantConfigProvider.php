<?php

namespace App\Provider\MultiTenant;

use App\Entity\WhiteLabel;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class TenantConfigProvider
{
    public function __construct(
        private readonly CacheInterface $cache
    ) {}

    /**
     * @return array<string, mixed>
     */
    public function getConfig(WhiteLabel $tenant): array
    {
        $key = 'tenant_config_' . $tenant->getId();

        return $this->cache->get($key, function (ItemInterface $item) use ($tenant): array {
            $item->expiresAfter(60);
            return $tenant->getConfig();
        });
    }
}