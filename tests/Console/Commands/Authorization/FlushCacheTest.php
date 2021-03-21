<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Console\Commands\Authorization;

use AbterPhp\Framework\Authorization\CacheManager;
use Opulence\Console\Responses\IResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FlushCacheTest extends TestCase
{
    private FlushCache $sut;

    /** @var CacheManager|MockObject */
    private $cacheManagerMock;

    public function setUp(): void
    {
        $this->cacheManagerMock = $this->getMockBuilder(CacheManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut = new FlushCache($this->cacheManagerMock);
    }

    public function testExecuteFlushesCache()
    {
        $responseMock = $this->getMockBuilder(IResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cacheManagerMock->expects($this->atLeastOnce())->method('clearAll');
        $responseMock->expects($this->atLeastOnce())->method('writeln');

        $this->sut->execute($responseMock);
    }
}
