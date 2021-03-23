<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Console\Commands\Assets;

use AbterPhp\Framework\Assets\CacheManager\ICacheManager;
use Opulence\Console\Responses\IResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FlushCacheTest extends TestCase
{
    /** @var FlushCache - System Under Test */
    protected FlushCache $sut;

    /** @var ICacheManager|MockObject */
    protected $cacheManagerMock;

    public function setUp(): void
    {
        $this->cacheManagerMock = $this->getMockBuilder(ICacheManager::class)->getMock();

        $this->sut = new FlushCache($this->cacheManagerMock);
    }

    public function testExecuteFlushesCache()
    {
        $responseMock = $this->getMockBuilder(IResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cacheManagerMock->expects($this->atLeastOnce())->method('flush');
        $responseMock->expects($this->atLeastOnce())->method('writeln');

        $this->sut->execute($responseMock);
    }

    public function testExecutesWritesResponseOnExceptions()
    {
        $ex = new \RuntimeException('foo');

        $responseMock = $this->getMockBuilder(IResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock->expects($this->atLeastOnce())->method('writeln');

        $this->cacheManagerMock->expects($this->any())->method('flush')->willThrowException($ex);

        $this->sut->execute($responseMock);
    }
}
