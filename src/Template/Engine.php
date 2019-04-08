<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

class Engine
{
    const ERROR_MSG_CACHING_FAILURE = 'Caching failure';

    const ERROR_INVALID_LOADER = 'Loaders must be an instance of %s';

    /** @var ILoader[] */
    protected $loaders = [];

    /** @var string[] */
    protected $templateTypes = [];

    /** @var string[][] */
    protected $allSubTemplateIds = [];

    /** @var Renderer */
    protected $renderer;

    /** @var CacheManager */
    protected $cacheManager;

    /**
     * Engine constructor.
     *
     * @param Renderer     $renderer
     * @param CacheManager $cache
     */
    public function __construct(Renderer $renderer, CacheManager $cache)
    {
        $this->renderer     = $renderer;
        $this->cacheManager = $cache;
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

        if (!$this->cacheManager->storeCacheData($cacheId, $this->allSubTemplateIds)) {
            throw new Exception(static::ERROR_MSG_CACHING_FAILURE);
        }

        if (!$this->cacheManager->storeDocument($cacheId, $content)) {
            throw new Exception(static::ERROR_MSG_CACHING_FAILURE);
        }

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
     *
     * @return bool
     */
    protected function hasValidCache(string $cacheId): bool
    {
        $cacheData = $this->cacheManager->getCacheData($cacheId);
        if ($cacheData === null) {
            return false;
        }

        return $this->renderer->hasAllValidLoaders($cacheData->getSubTemplates(), $cacheData->getDate());
    }
}
