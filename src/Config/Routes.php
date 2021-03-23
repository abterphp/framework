<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Config;

use AbterPhp\Framework\Constant\Env;
use Opulence\Environments\Environment;

class Routes
{
    public const ASSETS_PATH = '/:path';

    /** @var string|null */
    protected ?string $mediaUrl = null;

    /** @var string|null */
    protected ?string $cacheUrl = null;

    /** @var string|null */
    protected ?string $assetsPath = null;

    /**
     * @param string $mediaUrl
     */
    public function setMediaUrl(string $mediaUrl): void
    {
        $this->mediaUrl = $mediaUrl;
    }

    /**
     * @return string
     */
    public function getMediaUrl(): string
    {
        if (null !== $this->mediaUrl) {
            return $this->mediaUrl;
        }

        $this->mediaUrl = (string)Environment::getVar(Env::MEDIA_BASE_URL);

        return $this->mediaUrl;
    }

    /**
     * @param string $cacheUrl
     */
    public function setCacheUrl(string $cacheUrl): void
    {
        $this->cacheUrl = $cacheUrl;
    }

    /**
     * @return string
     */
    public function getCacheUrl(): string
    {
        if (null !== $this->cacheUrl) {
            return $this->cacheUrl;
        }

        $cachePath = Environment::getVar(Env::CACHE_BASE_PATH, '');
        if (!$cachePath) {
            return '';
        }

        $this->cacheUrl = sprintf(
            '%s%s%s',
            rtrim(static::getMediaUrl(), DIRECTORY_SEPARATOR),
            DIRECTORY_SEPARATOR,
            ltrim($cachePath, DIRECTORY_SEPARATOR)
        );

        return $this->cacheUrl;
    }

    /**
     * @param string $assetsPath
     */
    public function setAssetsPath(string $assetsPath): void
    {
        $this->assetsPath = $assetsPath;
    }

    /**
     * @return string
     */
    public function getAssetsPath(): string
    {
        if (null !== $this->assetsPath) {
            return $this->assetsPath;
        }

        $basePath = Environment::getVar(Env::CACHE_BASE_PATH, '');
        if (!$basePath) {
            return '';
        }

        $this->assetsPath = sprintf('%s%s', $basePath, static::ASSETS_PATH);

        return $this->assetsPath;
    }
}
