<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

use Opulence\Cache\ICacheBridge;

class CacheManager
{
    const CACHE_KEY_TEMPLATES = 'templates_%s';
    const CACHE_KEY_DOCUMENT  = 'document_%s';

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
     * @param string $cacheId
     *
     * @return CacheData|null
     */
    public function getCacheData(string $cacheId): ?CacheData
    {
        $key = $this->getCacheKey($cacheId);
        try {
            $payload = $this->cacheBridge->get($key);
        } catch (\Exception $e) {
            return null;
        }

        if (empty($payload) || !is_string($payload)) {
            return null;
        }

        $data = json_decode($payload, true);
        if (!is_array($data)) {
            return null;
        }

        $cacheData = new CacheData();

        if (array_key_exists(CacheData::PAYLOAD_KEY_DATE, $data)) {
            $cacheData->setDate($data[CacheData::PAYLOAD_KEY_DATE]);
        }

        if (array_key_exists(CacheData::PAYLOAD_KEY_SUBTEMPLATES, $data)) {
            $cacheData->setSubTemplates($data[CacheData::PAYLOAD_KEY_SUBTEMPLATES]);
        }

        return $cacheData;
    }

    /**
     * @param string $documentId
     * @param array  $blocks
     *
     * @return bool
     */
    public function storeCacheData(string $cacheId, array $blocks): bool
    {
        $cacheData = (new CacheData())->setSubTemplates($blocks);

        $payload = json_encode(
            [
                CacheData::PAYLOAD_KEY_DATE         => $cacheData->getDate(),
                CacheData::PAYLOAD_KEY_SUBTEMPLATES => $cacheData->getSubTemplates(),
            ]
        );

        $key = $this->getCacheKey($cacheId);

        $this->cacheBridge->set($key, $payload, PHP_INT_MAX);

        return $this->cacheBridge->has($key);
    }

    /**
     * @param string $cacheId
     *
     * @return string
     */
    public function getDocument(string $cacheId): string
    {
        $key = $this->getDocumentCacheKey($cacheId);

        try {
            return (string)$this->cacheBridge->get($key);
        } catch (\Exception $e) {
            return '';
        }
    }

    /**
     * @param string $cacheId
     * @param string $payload
     *
     * @return bool
     */
    public function storeDocument(string $cacheId, string $payload): bool
    {
        $key = $this->getDocumentCacheKey($cacheId);

        $this->cacheBridge->set($key, $payload, PHP_INT_MAX);

        return $this->cacheBridge->has($key);
    }

    public function flush()
    {
        $this->cacheBridge->flush();
    }

    /**
     * @param string $cacheId
     *
     * @return string
     */
    private function getCacheKey(string $cacheId): string
    {
        return sprintf(static::CACHE_KEY_TEMPLATES, $cacheId);
    }

    /**
     * @param string $cacheId
     *
     * @return string
     */
    private function getDocumentCacheKey(string $cacheId): string
    {
        return sprintf(static::CACHE_KEY_DOCUMENT, $cacheId);
    }
}
