<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

class Engine
{
    const ERROR_MSG_CACHING_FAILURE = 'Caching failure';

    /** @var ILoader[] */
    protected $loaders = [];

    /** @var string[][] */
    protected $allSubTemplateIds = [];

    /** @var Renderer */
    protected $renderer;

    /** @var CacheManager */
    protected $cacheManager;

    /** @var bool */
    protected $isCacheAllowed;

    /**
     * Engine constructor.
     *
     * @param Renderer     $renderer
     * @param CacheManager $cache
     * @param bool         $isCacheAllowed
     */
    public function __construct(Renderer $renderer, CacheManager $cache, bool $isCacheAllowed)
    {
        $this->renderer       = $renderer;
        $this->cacheManager   = $cache;
        $this->isCacheAllowed = $isCacheAllowed;
    }

    /**
     * Renders a list of templates
     * previously rendered templates can be referenced as variables
     * the last template rendered will be returned as a string
     *
     * @param string   $type
     * @param string   $documentId
     * @param string[] $templates
     * @param string[] $vars
     *
     * @return string
     */
    public function run(string $type, string $documentId, array $templates, array $vars): string
    {
        $cacheId = md5($type . '/' . $documentId);

        if ($this->hasValidCache($cacheId)) {
            return $this->cacheManager->getDocument($cacheId);
        }

        $this->allSubTemplateIds = [];

        $content = '';
        foreach ($templates as $key => $template) {
            $content    = $this->renderer->render($template, $vars);
            $vars[$key] = $content;
        }

        $this->updateCache($cacheId, $content);

        return $content;
    }

    /**
     * @return Renderer
     */
    public function getRenderer()
    {
        return $this->renderer;
    }

    /**
     * @param string $cacheId
     * @param string $content
     */
    protected function updateCache(string $cacheId, string $content): void
    {
        if (!$this->isCacheAllowed) {
            return;
        }

        if (!$this->cacheManager->storeCacheData($cacheId, $this->allSubTemplateIds)) {
            throw new Exception(static::ERROR_MSG_CACHING_FAILURE);
        }

        if (!$this->cacheManager->storeDocument($cacheId, $content)) {
            throw new Exception(static::ERROR_MSG_CACHING_FAILURE);
        }
    }

    /**
     * @param string $cacheId
     *
     * @return bool
     */
    protected function hasValidCache(string $cacheId): bool
    {
        if (!$this->isCacheAllowed) {
            return false;
        }

        $cacheData = $this->cacheManager->getCacheData($cacheId);
        if ($cacheData === null) {
            return false;
        }

        return $this->renderer->hasAllValidLoaders($cacheData->getSubTemplates(), $cacheData->getDate());
    }
}
