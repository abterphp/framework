<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Authorization;

use Opulence\Cache\ICacheBridge;

class CacheManager
{
    const CACHE_KEY = 'casbin_auth_collection';

    /** @var ICacheBridge */
    protected $cacheBridge;

    /**
     * Cache constructor.
     *
     * @param ICacheBridge $cacheBridge
     */
    public function __construct(ICacheBridge $cacheBridge)
    {
        $this->cacheBridge = $cacheBridge;
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function storeAll(array $data): bool
    {
        $payload = json_encode($data);

        $this->cacheBridge->set(static::CACHE_KEY, $payload, PHP_INT_MAX);

        return $this->cacheBridge->has(static::CACHE_KEY);
    }

    /**
     * @return array|null
     */
    public function getAll(): ?array
    {
        try {
            $payload = $this->cacheBridge->get(static::CACHE_KEY);
        } catch (\Exception $e) {
            return null;
        }

        if (!is_string($payload) || $payload === '') {
            return null;
        }

        return (array)json_decode($payload, true);
    }

    /**
     * @return int number of keys deleted
     */
    public function clearAll(): int
    {
        $this->cacheBridge->delete(static::CACHE_KEY);

        return 1;
    }
}
