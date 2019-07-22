<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

use AbterPhp\Framework\Config\Provider as ConfigProvider;
use PHPUnit\Framework\MockObject\MockObject;

class EngineTest extends \PHPUnit\Framework\TestCase
{
    /** @var Engine */
    protected $sut;

    /** @var Renderer|MockObject */
    protected $rendererMock;

    /** @var CacheManager|MockObject */
    protected $cacheManagerMock;

    /** @var ConfigProvider|MockObject */
    protected $configProvider;

    public function setUp()
    {
        parent::setUp();

        $this->rendererMock = $this->getMockBuilder(Renderer::class)
            ->disableOriginalConstructor()
            ->setMethods(['addLoader', 'hasAllValidLoaders', 'render'])
            ->getMock();

        $this->cacheManagerMock = $this->getMockBuilder(CacheManager::class)
            ->disableOriginalConstructor()
            ->setMethods(['getCacheData', 'storeCacheData', 'getDocument', 'storeDocument'])
            ->getMock();

        $this->configProvider = $this->getMockBuilder(ConfigProvider::class)
            ->disableOriginalConstructor()
            ->setMethods(['isCacheAllowed'])
            ->getMock();
        $this->configProvider->expects($this->any())->method('isCacheAllowed')->willReturn(true);

        $this->sut = new Engine($this->rendererMock, $this->cacheManagerMock, $this->configProvider);
    }

    /**
     * @return array
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

        $i = 0;
        foreach ($renderStubs as $renderStub) {
            $this->rendererMock->expects($this->at($i++))->method('render')->willReturn($renderStub);
        }

        $actualResult = $this->sut->run($type, $documentId, $templates, $vars);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @expectedException Exception
     */
    public function testRunThrowsExceptionIfDocumentStorageFails()
    {
        $templates = [];
        $vars      = [];

        $type       = 'foo';
        $documentId = 'foo0';

        $this->cacheManagerMock->expects($this->any())->method('storeCacheData')->willReturn(false);
        $this->cacheManagerMock->expects($this->never())->method('storeDocument');

        $this->sut->run($type, $documentId, $templates, $vars);
    }

    /**
     * @expectedException Exception
     */
    public function testRunThrowsExceptionIfCacheDataStorageFails()
    {
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
        $this->cacheManagerMock->expects($this->never())->method('storeCacheData');

        $this->sut->run($type, $documentId, $templates, $vars);
    }
}
