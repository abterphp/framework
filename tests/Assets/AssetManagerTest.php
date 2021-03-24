<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Assets;

use AbterPhp\Framework\Assets\CacheManager\ICacheManager;
use AbterPhp\Framework\Assets\Factory\Minifier as MinifierFactory;
use AbterPhp\Framework\Config\Routes;
use AbterPhp\Framework\Filesystem\IFileFinder;
use AbterPhp\Framework\TestDouble\MockFactory;
use League\Flysystem\FilesystemException;
use MatthiasMullie\Minify\CSS as CssMinifier;
use MatthiasMullie\Minify\JS as JsMinifier;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AssetManagerTest extends TestCase
{
    /** @var AssetManager - System Under Test */
    protected AssetManager $sut;

    /** @var CssMinifier|MockObject */
    protected $cssMinifierMock;

    /** @var JsMinifier|MockObject */
    protected $jsMinifierMock;

    /** @var MinifierFactory|MockObject */
    protected $minifierFactoryMock;

    /** @var IFileFinder|MockObject */
    protected $fileFinderMock;

    /** @var ICacheManager|MockObject */
    protected $cacheManagerMock;

    /** @var UrlFixer|MockObject */
    protected $urlFixerMock;

    /** @var Routes|MockObject */
    protected $routesMock;

    public function setUp(): void
    {
        $this->cssMinifierMock = $this->createMock(CssMinifier::class);

        $this->jsMinifierMock = $this->createMock(JsMinifier::class);

        $this->minifierFactoryMock = $this->createMock(MinifierFactory::class);
        $this->minifierFactoryMock = MockFactory::createMock(
            $this,
            MinifierFactory::class,
            [
                'createCssMinifier' => $this->cssMinifierMock,
                'createJsMinifier'  => $this->jsMinifierMock,
            ]
        );

        $this->fileFinderMock = $this->createMock(IFileFinder::class);

        $this->cacheManagerMock = $this->createMock(ICacheManager::class);

        $this->urlFixerMock = $this->createMock(UrlFixer::class);
        $this->urlFixerMock->expects($this->any())->method('fixCss')->willReturnArgument(0);

        $this->routesMock = $this->createMock(Routes::class);

        $this->sut = new AssetManager(
            $this->minifierFactoryMock,
            $this->fileFinderMock,
            $this->cacheManagerMock,
            $this->urlFixerMock,
            $this->routesMock
        );

        parent::setUp();
    }

    public function testAddCss(): void
    {
        $groupName = 'foo';
        $rawPath   = 'bar';
        $path      = 'bar.css';
        $content   = 'baz';

        $this->fileFinderMock->expects($this->once())->method('read')->with($path, $groupName)->willReturn($content);

        $this->cssMinifierMock->expects($this->once())->method('add')->with($content);

        $this->sut->addCss($groupName, $rawPath);
    }

    public function testAddCssWithPathWtihExtension(): void
    {
        $groupName = 'foo';
        $rawPath   = 'bar.css';
        $path      = 'bar.css';
        $content   = 'baz';

        $this->fileFinderMock->expects($this->once())->method('read')->with($path, $groupName)->willReturn($content);

        $this->cssMinifierMock->expects($this->once())->method('add')->with($content);

        $this->sut->addCss($groupName, $rawPath);
    }

    public function testAddJs(): void
    {
        $groupName = 'foo';
        $rawPath   = 'bar';
        $path      = 'bar.js';
        $content   = 'baz';

        $this->fileFinderMock->expects($this->once())->method('read')->with($path, $groupName)->willReturn($content);

        $this->jsMinifierMock->expects($this->once())->method('add')->with($content);

        $this->sut->addJs($groupName, $rawPath);
    }

    public function testAddJsWithPathWithExtension(): void
    {
        $groupName = 'foo';
        $rawPath   = 'bar.js';
        $path      = 'bar.js';
        $content   = 'baz';

        $this->fileFinderMock->expects($this->once())->method('read')->with($path, $groupName)->willReturn($content);

        $this->jsMinifierMock->expects($this->once())->method('add')->with($content);

        $this->sut->addJs($groupName, $rawPath);
    }

    public function testAddCssContent(): void
    {
        $groupName = 'foo';
        $content   = 'baz';

        $this->cssMinifierMock->expects($this->once())->method('add')->with($content);

        $this->sut->addCssContent($groupName, $content);
    }

    public function testAddJsContent(): void
    {
        $groupName = 'foo';
        $content   = 'baz';

        $this->jsMinifierMock->expects($this->once())->method('add')->with($content);

        $this->sut->addJsContent($groupName, $content);
    }

    public function testAddJsVar(): void
    {
        $groupName       = 'foo';
        $name            = 'fooer';
        $value           = ['baz' => ['bar', 'quix']];
        $expectedContent = 'var fooer = {"baz":["bar","quix"]};' . PHP_EOL;

        $this->jsMinifierMock->expects($this->once())->method('add')->with($expectedContent);

        $this->sut->addJsVar($groupName, $name, $value);
    }

    public function testRenderRawReturnsNullIfReadingFails(): void
    {
        $cachePath = 'foo.css';

        $actualResult = $this->sut->renderRaw($cachePath);

        $this->assertNull($actualResult);
    }

    public function testRenderRawWritesCacheIfContentIsRead(): void
    {
        $expectedResult = 'baz';

        $cachePath = 'foo.css';

        $this->fileFinderMock
            ->expects($this->once())
            ->method('read')
            ->with($cachePath)
            ->willReturn($expectedResult);

        $this->cacheManagerMock
            ->expects($this->once())
            ->method('write')
            ->with($cachePath, $expectedResult);

        $actualResult = $this->sut->renderRaw($cachePath);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testRenderCss(): void
    {
        $groupName = 'foo';
        $cachePath = 'foo.css';
        $content   = 'baz';

        $this->cssMinifierMock->expects($this->once())->method('minify')->willReturn($content);

        $this->cacheManagerMock->expects($this->once())->method('write')->with($cachePath, $content);

        $actualResult = $this->sut->renderCss($groupName);

        $this->assertSame($content, $actualResult);
    }

    public function testRenderJs(): void
    {
        $groupName = 'foo';
        $cachePath = 'foo.js';
        $content   = 'baz';

        $this->jsMinifierMock->expects($this->once())->method('minify')->willReturn($content);

        $this->cacheManagerMock->expects($this->once())->method('write')->with($cachePath, $content);

        $actualResult = $this->sut->renderJs($groupName);

        $this->assertSame($content, $actualResult);
    }

    public function testEnsureCssWebPathUsesCacheByDefault(): void
    {
        $groupName = 'foo';
        $cachePath = 'foo.css';
        $webPath   = '/baz';

        $this->cssMinifierMock->expects($this->never())->method('minify');

        $this->cacheManagerMock->expects($this->any())->method('fileExists')->willReturn(true);
        $this->cacheManagerMock->expects($this->any())->method('getWebPath')->with($cachePath)->willReturn($webPath);

        $actualResult = $this->sut->ensureCssWebPath($groupName);

        $this->assertSame($webPath, $actualResult);
    }

    public function testEnsureJsWebPathUsesCacheByDefault(): void
    {
        $groupName = 'foo';
        $cachePath = 'foo.js';
        $webPath   = '/baz';

        $this->jsMinifierMock->expects($this->never())->method('minify');

        $this->cacheManagerMock->expects($this->any())->method('fileExists')->willReturn(true);
        $this->cacheManagerMock->expects($this->any())->method('getWebPath')->with($cachePath)->willReturn($webPath);

        $actualResult = $this->sut->ensureJsWebPath($groupName);

        $this->assertSame($webPath, $actualResult);
    }

    public function testEnsureImgWebPathThrowsExceptionIfRenderingFails(): void
    {
        $this->expectException(FilesystemException::class);

        $cachePath = 'foo.js';

        $this->cacheManagerMock->expects($this->any())->method('fileExists')->willReturn(false);
        $this->fileFinderMock->expects($this->once())->method('read')->willReturn(null);

        $this->sut->ensureImgWebPath($cachePath);
    }

    public function testEnsureImgWebPathUsesCacheByDefault(): void
    {
        $cachePath = 'foo.js';
        $webPath   = '/baz';

        $this->cacheManagerMock->expects($this->any())->method('fileExists')->willReturn(true);
        $this->fileFinderMock->expects($this->never())->method('read');
        $this->cacheManagerMock->expects($this->any())->method('getWebPath')->with($cachePath)->willReturn($webPath);

        $actualResult = $this->sut->ensureImgWebPath($cachePath);

        $this->assertSame($webPath, $actualResult);
    }

    public function testEnsureCssWebPathRendersIfCacheDoesNotExist(): void
    {
        $groupName = 'foo';
        $cachePath = 'foo.css';
        $webPath   = '/baz';
        $content   = 'bar';

        $this->cssMinifierMock->expects($this->atLeastOnce())->method('minify')->willReturn($content);

        $this->cacheManagerMock->expects($this->any())->method('fileExists')->willReturn(false);
        $this->cacheManagerMock->expects($this->atLeastOnce())->method('write')->with($cachePath, $content);
        $this->cacheManagerMock->expects($this->any())->method('getWebPath')->with($cachePath)->willReturn($webPath);

        $actualResult = $this->sut->ensureCssWebPath($groupName);

        $this->assertSame($webPath, $actualResult);
    }

    public function testEnsureJsWebPathRendersIfCacheDoesNotExist(): void
    {
        $groupName = 'foo';
        $cachePath = 'foo.js';
        $webPath   = '/baz';
        $content   = 'bar';

        $this->jsMinifierMock->expects($this->atLeastOnce())->method('minify')->willReturn($content);

        $this->cacheManagerMock->expects($this->any())->method('fileExists')->willReturn(false);
        $this->cacheManagerMock->expects($this->atLeastOnce())->method('write')->with($cachePath, $content);
        $this->cacheManagerMock->expects($this->any())->method('getWebPath')->with($cachePath)->willReturn($webPath);

        $actualResult = $this->sut->ensureJsWebPath($groupName);

        $this->assertSame($webPath, $actualResult);
    }

    public function testEnsureImgWebPathRendersIfCacheDoesNotExist(): void
    {
        $cachePath = 'foo.js';
        $webPath   = '/baz';
        $content   = 'bar';

        $this->cacheManagerMock->expects($this->any())->method('fileExists')->willReturn(false);
        $this->fileFinderMock->expects($this->atLeastOnce())->method('read')->willReturn($content);
        $this->cacheManagerMock->expects($this->any())->method('getWebPath')->with($cachePath)->willReturn($webPath);

        $actualResult = $this->sut->ensureImgWebPath($cachePath);

        $this->assertSame($webPath, $actualResult);
    }
}
