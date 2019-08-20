<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

use Opulence\Cache\ICacheBridge;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CacheManagerTest extends TestCase
{
    /** @var CacheManager - System Under Test */
    protected $sut;

    /** @var ICacheBridge|MockObject */
    protected $cacheBridgeMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->cacheBridgeMock = $this->createMock(ICacheBridge::class);

        $this->sut = new CacheManager($this->cacheBridgeMock);
    }

    public function testGetCacheDataReturnsNullIfCacheBridgeThrowsException()
    {
        $cacheId = 'foo';

        $this->cacheBridgeMock->expects($this->any())->method('get')->willThrowException(new \Exception());

        $actualResult = $this->sut->getCacheData($cacheId);

        $this->assertNull($actualResult);
    }

    /**
     * @return array
     */
    public function getCacheDataProvider(): array
    {
        return [
            'simple'                       => [null, null],
            'bool-payload'                 => [true, null],
            'int-payload'                  => [1, null],
            'float-payload'                => [1.1, null],
            'object-payload'               => [new \stdClass(), null],
            'array-payload'                => [[], null],
            'string-json-payload'          => ['""', null],
            'empty-array-json-payload'     => ['[]', new CacheData()],
            'empty-object-json-payload'    => ['{}', new CacheData()],
            'no-subtemplates-json-payload' => [
                '{"date":"2019-03-01 13:59:59","subTemplates":[]}',
                new CacheData(),
            ],
            'full-json-payload'            => [
                '{"date":"2019-03-01 13:59:59","subTemplates":{"A":"A","B":"B"}}',
                (new CacheData())->setSubTemplates(['A' => 'A', 'B' => 'B']),
            ],
        ];
    }

    /**
     * @dataProvider getCacheDataProvider
     *
     * @param mixed          $payload
     * @param CacheData|null $expectedResult
     */
    public function testGetCacheData($payload, ?CacheData $expectedResult)
    {
        $cacheId = 'foo';

        $this->cacheBridgeMock->expects($this->any())->method('get')->willReturn($payload);

        $actualResult = $this->sut->getCacheData($cacheId);

        $this->assertEquals(gettype($expectedResult), gettype($actualResult));

        if (!($actualResult instanceof CacheData) || !($expectedResult instanceof CacheData)) {
            return;
        }

        $this->assertEquals($expectedResult->getSubTemplates(), $actualResult->getSubTemplates());
    }

    public function testStoreCacheDataCallsCacheBridge()
    {
        $cacheId = 'foo';
        $blocks  = ['A' => 'A', 'B' => 'B'];

        $cacheData = (new CacheData())->setSubTemplates($blocks);

        $payload = json_encode(
            [
                CacheData::PAYLOAD_KEY_DATE         => $cacheData->getDate(),
                CacheData::PAYLOAD_KEY_SUBTEMPLATES => $cacheData->getSubTemplates(),
            ]
        );
        $key     = sprintf(CacheManager::CACHE_KEY_TEMPLATES, $cacheId);

        $this->cacheBridgeMock->expects($this->once())->method('set')->with($key, $payload, PHP_INT_MAX);
        $this->cacheBridgeMock->expects($this->once())->method('has')->with($key);

        $this->sut->storeCacheData($cacheId, $blocks);
    }

    public function testGetDocumentCallsCacheBridge()
    {
        $cacheId        = 'foo';
        $expectedResult = 'bar';

        $key = sprintf(CacheManager::CACHE_KEY_DOCUMENT, $cacheId);

        $this->cacheBridgeMock->expects($this->once())->method('get')->with($key)->willReturn($expectedResult);

        $actualResult = $this->sut->getDocument($cacheId);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testGetDocumentReturnsEmptyIfCacheBridgeThrowsException()
    {
        $cacheId = 'foo';

        $this->cacheBridgeMock->expects($this->once())->method('get')->willThrowException(new \Exception());

        $actualResult = $this->sut->getDocument($cacheId);

        $this->assertSame('', $actualResult);
    }

    public function testStoreDocumentCallsCacheBridge()
    {
        $cacheId = 'foo';

        $payload = 'abc';
        $key     = sprintf(CacheManager::CACHE_KEY_DOCUMENT, $cacheId);

        $this->cacheBridgeMock->expects($this->once())->method('set')->with($key, $payload, PHP_INT_MAX);
        $this->cacheBridgeMock->expects($this->once())->method('has')->with($key);

        $this->sut->storeDocument($cacheId, $payload);
    }

    public function testFlushCallsCacheBridge()
    {
        $this->cacheBridgeMock->expects($this->once())->method('flush');

        $this->sut->flush();
    }
}
