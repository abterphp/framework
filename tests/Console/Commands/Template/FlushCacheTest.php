<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Console\Commands\Template;

use AbterPhp\Framework\Template\CacheManager;
use Opulence\Console\Responses\IResponse;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FlushCacheTest extends TestCase
{
    /** @var FlushCache - System Under Test */
    protected FlushCache $sut;

    /** @var CacheManager|MockObject */
    protected $cacheManagerMock;

    public function setUp(): void
    {
        $this->cacheManagerMock = $this->getMockBuilder(CacheManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->sut = new FlushCache($this->cacheManagerMock);
    }

    public function testExecuteFlushesCache(): void
    {
        $responseMock = $this->getMockBuilder(IResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->cacheManagerMock->expects($this->atLeastOnce())->method('flush');
        $responseMock->expects($this->atLeastOnce())->method('writeln');

        $this->sut->execute($responseMock);
    }
}
