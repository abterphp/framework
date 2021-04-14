<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Assets;

use AbterPhp\Framework\Assets\CacheManager\ICacheManager;
use AbterPhp\Framework\Assets\Factory\Minifier as MinifierFactory;
use AbterPhp\Framework\Config\Routes;
use AbterPhp\Framework\Filesystem\IFileFinder;
use League\Flysystem\FilesystemException;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\UnreadableFileEncountered;
use MatthiasMullie\Minify\CSS as CssMinifier;
use MatthiasMullie\Minify\JS as JsMinifier;

/**
 * @SuppressWarnings(PHPMD.TooManyPublicMethods)
 */
class AssetManager
{
    protected const EXT_CSS = '.css';
    protected const EXT_JS  = '.js';

    protected MinifierFactory $minifierFactory;

    protected IFileFinder $fileFinder;

    protected ICacheManager $cacheManager;

    protected UrlFixer $urlFixer;

    protected Routes $routes;

    /** @var array<string,JsMinifier> */
    protected array $jsMinifiers = [];

    /** @var array<string,CssMinifier> */
    protected array $cssMinifiers = [];

    /**
     * AssetManager constructor.
     *
     * @param MinifierFactory $minifierFactory
     * @param IFileFinder     $fileFinder
     * @param ICacheManager   $cacheManager
     * @param UrlFixer        $urlFixer
     * @param Routes          $routes
     */
    public function __construct(
        MinifierFactory $minifierFactory,
        IFileFinder $fileFinder,
        ICacheManager $cacheManager,
        UrlFixer $urlFixer,
        Routes $routes
    ) {
        $this->minifierFactory = $minifierFactory;
        $this->fileFinder      = $fileFinder;
        $this->cacheManager    = $cacheManager;
        $this->urlFixer        = $urlFixer;
        $this->routes          = $routes;
    }

    /**
     * @param string $groupName
     * @param string $rawPath
     *
     * @throws FilesystemException
     */
    public function addCss(string $groupName, string $rawPath): void
    {
        $fixedPath = $rawPath;
        if ($this->getExtension($rawPath) !== static:: EXT_CSS) {
            $fixedPath = $rawPath . static::EXT_CSS;
        }

        $content = $this->fileFinder->read($fixedPath, $groupName);
        if (null === $content) {
            throw new UnreadableFileEncountered("not found: " . $fixedPath);
        }

        $fixedContent = $this->urlFixer->fixCss($content, $fixedPath);

        $this->getCssMinifier($groupName)->add($fixedContent);
    }

    /**
     * @param string $groupName
     * @param string $rawPath
     *
     * @throws FilesystemException
     */
    public function addJs(string $groupName, string $rawPath): void
    {
        $fixedPath = $rawPath;
        if ($this->getExtension($rawPath) !== static:: EXT_JS) {
            $fixedPath = $rawPath . static::EXT_JS;
        }

        $content = $this->fileFinder->read($fixedPath, $groupName);
        if (null === $content) {
            throw new UnreadableFileEncountered("not found: " . $fixedPath);
        }

        $this->getJsMinifier($groupName)->add($content);
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function getExtension(string $path): string
    {
        $dotRPos = strrpos($path, '.');

        if ($dotRPos === false) {
            return '';
        }

        return substr($path, $dotRPos);
    }

    /**
     * @param string $groupName
     * @param string $content
     */
    public function addCssContent(string $groupName, string $content): void
    {
        $this->getCssMinifier($groupName)->add($content);
    }

    /**
     * @param string $groupName
     * @param string $content
     */
    public function addJsContent(string $groupName, string $content): void
    {
        $this->getJsMinifier($groupName)->add($content);
    }

    /**
     * @param string $groupName
     * @param string $name
     * @param mixed  $value
     */
    public function addJsVar(string $groupName, string $name, $value): void
    {
        $this->getJsMinifier($groupName)->add(sprintf("var %s = %s;\n", $name, json_encode($value)));
    }

    /**
     * @param string $groupName
     *
     * @return string
     * @throws UnableToWriteFile
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
     * @throws UnableToWriteFile
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
     * @throws FilesystemException
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
     * @throws UnableToReadFile
     * @throws UnableToWriteFile
     */
    public function ensureCssWebPath(string $groupName): string
    {
        $cachePath = $groupName . static::EXT_CSS;

        if (!$this->cacheManager->fileExists($cachePath)) {
            $this->renderCss($groupName);
        }

        return $this->getWebPath($cachePath);
    }

    /**
     * @param string $groupName
     *
     * @return string
     * @throws UnableToReadFile
     * @throws UnableToWriteFile
     */
    public function ensureJsWebPath(string $groupName): string
    {
        $cachePath = $groupName . static::EXT_JS;

        if (!$this->cacheManager->fileExists($cachePath)) {
            $this->renderJs($groupName);
        }

        return $this->getWebPath($cachePath);
    }

    /**
     * @param string $cachePath
     *
     * @return string
     * @throws FilesystemException
     */
    public function ensureImgWebPath(string $cachePath): string
    {
        if (!$this->cacheManager->fileExists($cachePath)) {
            if ($this->renderRaw($cachePath) === null) {
                throw UnableToReadFile::fromLocation($cachePath);
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

        $cachePath = $this->routes->getCacheUrl();
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
