<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Assets;

use AbterPhp\Framework\Assets\CacheManager\ICacheManager;
use AbterPhp\Framework\Assets\Factory\Minifier as MinifierFactory;
use AbterPhp\Framework\Config\Routes;
use AbterPhp\Framework\Filesystem\IFileFinder;
use League\Flysystem\FileNotFoundException;
use MatthiasMullie\Minify\CSS as CssMinifier;
use MatthiasMullie\Minify\JS as JsMinifier;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class AssetManager
{
    protected const EXT_CSS = '.css';
    protected const EXT_JS  = '.js';

    /** @var MinifierFactory */
    protected $minifierFactory;

    /** @var IFileFinder */
    protected $fileFinder;

    /** @var ICacheManager */
    protected $cacheManager;

    /** @var JsMinifier[] */
    protected $jsMinifiers = [];

    /** @var CssMinifier[] */
    protected $cssMinifiers = [];

    /**
     * AssetManager constructor.
     *
     * @param MinifierFactory $minifierFactory
     * @param IFileFinder     $fileFinder
     * @param ICacheManager   $cacheManager
     */
    public function __construct(
        MinifierFactory $minifierFactory,
        IFileFinder $fileFinder,
        ICacheManager $cacheManager
    ) {
        $this->minifierFactory = $minifierFactory;
        $this->fileFinder      = $fileFinder;
        $this->cacheManager    = $cacheManager;
    }

    /**
     * @param string $groupName
     * @param string $rawPath
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function addCss(string $groupName, string $rawPath)
    {
        $fixedPath = $rawPath;
        if ($this->getExtension($rawPath) !== static:: EXT_CSS) {
            $fixedPath = $rawPath . static::EXT_CSS;
        }

        $content = $this->fileFinder->read($fixedPath, $groupName);

        $this->getCssMinifier($groupName)->add($content);
    }

    /**
     * @param string $groupName
     * @param string $rawPath
     *
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function addJs(string $groupName, string $rawPath)
    {
        $fixedPath = $rawPath;
        if ($this->getExtension($rawPath) !== static:: EXT_JS) {
            $fixedPath = $rawPath . static::EXT_JS;
        }

        $content = $this->fileFinder->read($fixedPath, $groupName);

        $this->getJsMinifier($groupName)->add($content);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function getExtension(string $path): string
    {
        $dotrpos = strrpos($path, '.');

        if ($dotrpos === false) {
            return '';
        }

        return substr($path, $dotrpos);
    }

    /**
     * @param string $groupName
     * @param string $content
     */
    public function addCssContent(string $groupName, string $content)
    {
        $this->getCssMinifier($groupName)->add($content);
    }

    /**
     * @param string $groupName
     * @param string $content
     */
    public function addJsContent(string $groupName, string $content)
    {
        $this->getJsMinifier($groupName)->add($content);
    }

    /**
     * @param string $groupName
     *
     * @return string
     * @throws \League\Flysystem\FileExistsException
     */
    public function renderCss(string $groupName): string
    {
        $content   = $this->getCssMinifier($groupName)->minify();
        $cachePath = $groupName . static::EXT_CSS;

        $this->cacheManager->write($cachePath, $content);

        return $content;
    }

    /**
     * @param string $groupName
     *
     * @return string
     * @throws \League\Flysystem\FileExistsException
     */
    public function renderJs(string $groupName): string
    {
        $content   = $this->getJsMinifier($groupName)->minify();
        $cachePath = $groupName . static::EXT_JS;

        $this->cacheManager->write($cachePath, $content);

        return $content;
    }

    /**
     * @param string $cachePath
     *
     * @return string|null
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function renderRaw(string $cachePath): ?string
    {
        $content = $this->fileFinder->read($cachePath);
        if (null === $content) {
            return null;
        }

        $this->cacheManager->write($cachePath, $content);

        return $content;
    }

    /**
     * @param string $groupName
     *
     * @return string
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function ensureCssWebPath(string $groupName): string
    {
        $cachePath = $groupName . static::EXT_CSS;

        if (!$this->cacheManager->has($cachePath)) {
            $this->renderCss($groupName);
        }

        return $this->getWebPath($cachePath);
    }

    /**
     * @param string $groupName
     *
     * @return string
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function ensureJsWebPath(string $groupName): string
    {
        $cachePath = $groupName . static::EXT_JS;

        if (!$this->cacheManager->has($cachePath)) {
            $this->renderJs($groupName);
        }

        return $this->getWebPath($cachePath);
    }

    /**
     * @param string $cachePath
     *
     * @return string
     * @throws \League\Flysystem\FileExistsException
     * @throws \League\Flysystem\FileNotFoundException
     */
    public function ensureImgWebPath(string $cachePath): string
    {
        if (!$this->cacheManager->has($cachePath)) {
            if ($this->renderRaw($cachePath) === null) {
                throw new FileNotFoundException($cachePath);
            }
        }

        return $this->getWebPath($cachePath);
    }

    /**
     * @param string $cachePath
     *
     * @return string
     */
    protected function getWebPath(string $cachePath): string
    {
        $path = $this->cacheManager->getWebPath($cachePath);
        if (!$path) {
            return $path;
        }

        $cachePath = Routes::getCacheUrl();
        if (!$cachePath) {
            return $path;
        }

        return sprintf(
            '%s%s%s',
            $cachePath,
            DIRECTORY_SEPARATOR,
            ltrim($path, DIRECTORY_SEPARATOR)
        );
    }

    /**
     * @param string $key
     *
     * @return CssMinifier
     */
    protected function getCssMinifier(string $key): CssMinifier
    {
        if (!array_key_exists($key, $this->cssMinifiers)) {
            $this->cssMinifiers[$key] = $this->minifierFactory->createCssMinifier();
        }

        return $this->cssMinifiers[$key];
    }

    /**
     * @param string $key
     *
     * @return JsMinifier
     */
    protected function getJsMinifier(string $key): JsMinifier
    {
        if (!array_key_exists($key, $this->jsMinifiers)) {
            $this->jsMinifiers[$key] = $this->minifierFactory->createJsMinifier();
        }

        return $this->jsMinifiers[$key];
    }
}
