<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class EngineTest extends TestCase
{
    /** @var Engine - System Under Test */
    protected $sut;

    /** @var Renderer|MockObject */
    protected $rendererMock;

    /** @var CacheManager|MockObject */
    protected $cacheManagerMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->rendererMock = $this->createMock(Renderer::class);

        $this->cacheManagerMock = $this->createMock(CacheManager::class);

        $this->sut = new Engine($this->rendererMock, $this->cacheManagerMock, true);
    }

    /**
     * @return array[]
     */
    public function runProvider(): array
    {
        return [
            'empty'                     => [[], [], [], ''],
            'rendered-is-returned'      => [['foo' => ''], [], ['rendered'], 'rendered'],
            'last-rendered-is-returned' => [['foo' => '', 'bar' => ''], [], ['baz', 'rendered'], 'rendered'],
        ];
    }

    /**
     * @dataProvider runProvider
     *
     * @param array  $templates
     * @param array  $vars
     * @param array  $renderStubs
     * @param string $expectedResult
     */
    public function testRun(array $templates, array $vars, array $renderStubs, string $expectedResult)
    {
        $type       = 'foo';
        $documentId = 'foo0';

        $this->cacheManagerMock->expects($this->any())->method('storeCacheData')->willReturn(true);
        $this->cacheManagerMock->expects($this->any())->method('storeDocument')->willReturn(true);

        $this->rendererMock
            ->expects($this->exactly(count($renderStubs)))
            ->method('render')
            ->willReturnOnConsecutiveCalls(...$renderStubs);

        $actualResult = $this->sut->run($type, $documentId, $templates, $vars);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testRunThrowsExceptionIfDocumentStorageFails()
    {
        $this->expectException(Exception::class);

        $templates = [];
        $vars      = [];

        $type       = 'foo';
        $documentId = 'foo0';

        $this->cacheManagerMock->expects($this->any())->method('storeCacheData')->willReturn(false);
        $this->cacheManagerMock->expects($this->never())->method('storeDocument');

        $this->sut->run($type, $documentId, $templates, $vars);
    }

    public function testRunThrowsExceptionIfCacheDataStorageFails()
    {
        $this->expectException(Exception::class);

        $templates = [];
        $vars      = [];

        $type       = 'foo';
        $documentId = 'foo0';

        $this->cacheManagerMock->expects($this->any())->method('storeCacheData')->willReturn(true);
        $this->cacheManagerMock->expects($this->any())->method('storeDocument')->willReturn(false);

        $this->sut->run($type, $documentId, $templates, $vars);
    }

    public function testRunWithValidCache()
    {
        $expectedResult = 'bar';

        $type       = 'foo';
        $documentId = 'foo0';
        $templates  = [];
        $vars       = [];

        $this->cacheManagerMock->expects($this->any())->method('getCacheData')->willReturn(new CacheData());
        $this->rendererMock->expects($this->any())->method('hasAllValidLoaders')->willReturn(true);
        $this->cacheManagerMock->expects($this->once())->method('getDocument')->willReturn($expectedResult);
        $this->cacheManagerMock->expects($this->never())->method('storeCacheData');

        $this->sut->run($type, $documentId, $templates, $vars);
    }

    public function testRunWithoutValidCache()
    {
        $sut = new Engine($this->rendererMock, $this->cacheManagerMock, false);

        $type       = 'foo';
        $documentId = 'foo0';
        $templates  = [];
        $vars       = [];

        $this->cacheManagerMock->expects($this->never())->method('getCacheData');
        $this->rendererMock->expects($this->any())->method('hasAllValidLoaders')->willReturn(true);
        $this->cacheManagerMock->expects($this->never())->method('getDocument');
        $this->cacheManagerMock->expects($this->never())->method('storeCacheData');

        $sut->run($type, $documentId, $templates, $vars);
    }

    public function testGetRendererReturnsRenderer()
    {
        $actualResult = $this->sut->getRenderer();

        $this->assertInstanceOf(Renderer::class, $actualResult);
    }
}
