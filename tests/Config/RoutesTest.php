<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Config;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use PHPUnit\Framework\TestCase;

class RoutesTest extends TestCase
{
    private const MEDIA_BASE_URL = "/media";

    /** @var Routes - System Under Test */
    protected Routes $sut;

    public function setUp(): void
    {
        $this->sut = new Routes();

        Environment::setVar(Env::MEDIA_BASE_URL, self::MEDIA_BASE_URL);
        Environment::unsetVar(Env::CACHE_BASE_PATH);

        parent::setUp();
    }

    public function testGetMediaUrlPreset(): void
    {
        $mediaBaseUrl = 'foo';

        $this->sut->setMediaUrl($mediaBaseUrl);

        $actualResult = $this->sut->getMediaUrl();

        $this->assertEquals($mediaBaseUrl, $actualResult);
    }

    public function testGetMediaUrlFromEnvVar(): void
    {
        $mediaBaseUrl = 'foo';

        Environment::setVar(Env::MEDIA_BASE_URL, $mediaBaseUrl);

        $actualResult = $this->sut->getMediaUrl();

        $this->assertEquals($mediaBaseUrl, $actualResult);
    }

    public function testGetCacheUrlPreset(): void
    {
        $cacheUrl = 'foo';

        $this->sut->setCacheUrl($cacheUrl);

        $actualResult = $this->sut->getCacheUrl();

        $this->assertEquals($cacheUrl, $actualResult);
    }

    public function testGetCacheUrlFromEmptyEnvVar(): void
    {
        $cacheBasePath = '';

        Environment::setVar(Env::CACHE_BASE_PATH, $cacheBasePath);

        $actualResult = $this->sut->getCacheUrl();

        $this->assertEquals($cacheBasePath, $actualResult);
    }

    public function testGetCacheUrlFromEnvVar(): void
    {
        $cacheBasePath = 'foo';
        $expectedBasePath = self::MEDIA_BASE_URL . '/foo';

        Environment::setVar(Env::CACHE_BASE_PATH, $cacheBasePath);

        $actualResult = $this->sut->getCacheUrl();

        $this->assertEquals($expectedBasePath, $actualResult);
    }

    public function testGetAssetsPathPreset(): void
    {
        $cacheUrl = 'foo';

        $this->sut->setAssetsPath($cacheUrl);

        $actualResult = $this->sut->getAssetsPath();

        $this->assertEquals($cacheUrl, $actualResult);
    }

    public function testGetAssetsPathFromEmptyEnvVar(): void
    {
        $cacheBasePath = '';

        Environment::setVar(Env::CACHE_BASE_PATH, $cacheBasePath);

        $actualResult = $this->sut->getAssetsPath();

        $this->assertEquals($cacheBasePath, $actualResult);
    }

    public function testGetAssetsPathFromEnvVar(): void
    {
        $cacheBasePath = 'foo';
        $expectedBasePath = 'foo/:path';

        Environment::setVar(Env::CACHE_BASE_PATH, $cacheBasePath);

        $actualResult = $this->sut->getAssetsPath();

        $this->assertEquals($expectedBasePath, $actualResult);
    }
}
