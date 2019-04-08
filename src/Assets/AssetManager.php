<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Assets;

use AbterPhp\Framework\Assets\Factory\Minifier as MinifierFactory;
use MatthiasMullie\Minify\CSS as CssMinifier;
use MatthiasMullie\Minify\JS as JsMinifier;

class AssetManager
{
    const FILE_EXTENSION_CSS = '.css';
    const FILE_EXTENSION_JS  = '.js';

    /** @var MinifierFactory */
    protected $minifierFactory;

    /** @var string */
    protected $dirRootJs;

    /** @var string */
    protected $dirRootCss;

    /** @var string */
    protected $dirCacheJs;

    /** @var string */
    protected $dirCacheCss;

    /** @var string */
    protected $pathCacheJs;

    /** @var string */
    protected $pathCacheCss;

    /** @var bool */
    protected $isCacheAllowed;

    /** @var JsMinifier[] */
    protected $jsMinifiers = [];

    /** @var CssMinifier[] */
    protected $cssMinifiers = [];

    /**
     * Assets constructor.
     *
     * @param MinifierFactory $minifierFactory
     * @param string          $dirRootJs
     * @param string          $dirRootCss
     * @param string          $dirCacheJs
     * @param string          $dirCacheCss
     * @param string          $pathCacheJs
     * @param string          $pathCacheCss
     * @param bool            $isCacheAllowed
     */
    public function __construct(
        MinifierFactory $minifierFactory,
        string $dirRootJs,
        string $dirRootCss,
        string $dirCacheJs,
        string $dirCacheCss,
        string $pathCacheJs,
        string $pathCacheCss,
        bool $isCacheAllowed
    ) {
        $this->minifierFactory = $minifierFactory;

        $this->dirRootJs      = rtrim($dirRootJs, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->dirRootCss     = rtrim($dirRootCss, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->dirCacheJs     = rtrim($dirCacheJs, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->dirCacheCss    = rtrim($dirCacheCss, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->pathCacheJs    = rtrim($pathCacheJs, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->pathCacheCss   = rtrim($pathCacheCss, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        $this->isCacheAllowed = $isCacheAllowed;
    }

    /**
     * @return string
     */
    public function getDirCacheJs()
    {
        return $this->dirCacheJs;
    }

    /**
     * @return string
     */
    public function getDirCacheCss()
    {
        return $this->dirCacheCss;
    }

    /**
     * @param string $key
     * @param string $path
     */
    public function addCss(string $key, string $path)
    {
        $fullPath = $this->dirRootCss . ltrim($path, DIRECTORY_SEPARATOR);

        $this->getCssMinifier($key)->add($fullPath);
    }

    /**
     * @param string $key
     * @param string $path
     */
    public function addJs(string $key, string $path)
    {
        $fullPath = $this->dirRootJs . ltrim($path, DIRECTORY_SEPARATOR);

        $this->getJsMinifier($key)->add($fullPath);
    }

    /**
     * @param string $key
     * @param string $content
     */
    public function addCssContent(string $key, string $content)
    {
        $this->getCssMinifier($key)->add($content);
    }

    /**
     * @param string $key
     * @param string $content
     */
    public function addJsContent(string $key, string $content)
    {
        $this->getJsMinifier($key)->add($content);
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function renderJs(string $key): string
    {
        $content = $this->getJsMinifier($key)->minify($this->getJsCachePath($key));

        return $content;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function renderCss(string $key): string
    {
        $minifier = $this->getCssMinifier($key);
        $path     = $this->getCssCachePath($key);

        $content = $minifier->minify($path);

        return $content;
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function ensureJsWebPath(string $key): string
    {
        if (empty($key)) {
            throw new \InvalidArgumentException('Key must not be empty.');
        }

        $filePath = $this->getJsCachePath($key);
        if (!is_file($filePath) || !$this->isCacheAllowed) {
            if (!$this->hasJsMinifier($key)) {
                return '';
            }

            $this->getJsMinifier($key)->minify($filePath);
        }

        return $this->getJsWebPath($key, $this->getVersion($filePath));
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function ensureCssWebPath(string $key): string
    {
        if (empty($key)) {
            throw new \InvalidArgumentException('Key must not be empty.');
        }

        $filePath = $this->getCssCachePath($key);
        if (!is_file($filePath) || !$this->isCacheAllowed) {
            if (!$this->hasCssMinifier($key)) {
                return '';
            }

            $this->getCssMinifier($key)->minify($filePath);
        }

        return $this->getCssWebPath($key, $this->getVersion($filePath));
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function getJsCachePath(string $key): string
    {
        return sprintf(
            '%s%s%s',
            $this->dirCacheJs,
            $key,
            static::FILE_EXTENSION_JS
        );
    }

    /**
     * @param string $key
     *
     * @return string
     */
    protected function getCssCachePath(string $key): string
    {
        return sprintf(
            '%s%s%s',
            $this->dirCacheCss,
            $key,
            static::FILE_EXTENSION_CSS
        );
    }

    /**
     * @param string $path
     *
     * @return string
     */
    protected function getVersion(string $path): string
    {
        if (!$this->isCacheAllowed) {
            return '';
        }
        
        return substr(md5((string)filectime($path)), 0, 5);
    }

    /**
     * @param string $key
     * @param string $version
     *
     * @return string
     */
    protected function getJsWebPath(string $key, string $version): string
    {
        return sprintf(
            '%s%s%s?%s',
            $this->pathCacheJs,
            $key,
            static::FILE_EXTENSION_JS,
            $version
        );
    }

    /**
     * @param string $key
     * @param string $version
     *
     * @return string
     */
    protected function getCssWebPath(string $key, string $version): string
    {
        return sprintf(
            '%s%s%s?%s',
            $this->pathCacheCss,
            $key,
            static::FILE_EXTENSION_CSS,
            $version
        );
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    protected function hasCssMinifier(string $key): bool
    {
        return array_key_exists($key, $this->cssMinifiers);
    }

    /**
     * @param string $key
     *
     * @return bool
     */
    protected function hasJsMinifier(string $key): bool
    {
        return array_key_exists($key, $this->jsMinifiers);
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
