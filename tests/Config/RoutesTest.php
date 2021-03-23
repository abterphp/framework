<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Config;

use AbterPhp\Framework\Constant\Env;
use AbterPhp\Framework\Environments\Environment;
use PHPUnit\Framework\TestCase;

class RoutesTest extends TestCase
{
    /** @var Routes - System Under Test */
    protected Routes $sut;

    public function setUp(): void
    {
        $this->sut = new Routes();

        parent::setUp();
    }

    public function tearDown(): void
    {
        Environment::unsetVar(Env::MEDIA_BASE_URL);
        Environment::unsetVar(Env::CACHE_BASE_PATH);
    }

    public function testGetMediaUrlPreset()
    {
        $mediaBaseUrl = 'foo';

        $this->sut->setMediaUrl($mediaBaseUrl);

        $actualResult = $this->sut->getMediaUrl();

        $this->assertEquals($mediaBaseUrl, $actualResult);
    }

    public function testGetMediaUrlFromEnvVar()
    {
        $mediaBaseUrl = 'foo';

        Environment::setVar(Env::MEDIA_BASE_URL, $mediaBaseUrl);

        $actualResult = $this->sut->getMediaUrl();

        $this->assertEquals($mediaBaseUrl, $actualResult);
    }

    public function testGetCacheUrlPreset()
    {
        $cacheUrl = 'foo';

        $this->sut->setCacheUrl($cacheUrl);

        $actualResult = $this->sut->getCacheUrl();

        $this->assertEquals($cacheUrl, $actualResult);
    }

    public function testGetCacheUrlFromEmptyEnvVar()
    {
        $cacheBasePath = '';

        Environment::setVar(Env::CACHE_BASE_PATH, $cacheBasePath);

        $actualResult = $this->sut->getCacheUrl();

        $this->assertEquals($cacheBasePath, $actualResult);
    }

    public function testGetCacheUrlFromEnvVar()
    {
        $cacheBasePath = 'foo';
        $expectedBasePath = '/foo';

        Environment::setVar(Env::CACHE_BASE_PATH, $cacheBasePath);

        $actualResult = $this->sut->getCacheUrl();

        $this->assertEquals($expectedBasePath, $actualResult);
    }

    public function testGetAssetsPathPreset()
    {
        $cacheUrl = 'foo';

        $this->sut->setAssetsPath($cacheUrl);

        $actualResult = $this->sut->getAssetsPath();

        $this->assertEquals($cacheUrl, $actualResult);
    }

    public function testGetAssetsPathFromEmptyEnvVar()
    {
        $cacheBasePath = '';

        Environment::setVar(Env::CACHE_BASE_PATH, $cacheBasePath);

        $actualResult = $this->sut->getAssetsPath();

        $this->assertEquals($cacheBasePath, $actualResult);
    }

    public function testGetAssetsPathFromEnvVar()
    {
        $cacheBasePath = 'foo';
        $expectedBasePath = 'foo/:path';

        Environment::setVar(Env::CACHE_BASE_PATH, $cacheBasePath);

        $actualResult = $this->sut->getAssetsPath();

        $this->assertEquals($expectedBasePath, $actualResult);
    }
}
