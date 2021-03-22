<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Console\Commands\AbterPhp;

use Opulence\Console\Commands\CommandCollection;
use Opulence\Console\Responses\IResponse;
use PHPUnit\Framework\TestCase;

class FlushCacheTest extends TestCase
{
    private FlushCache $sut;

    public function setUp(): void
    {
        $this->sut = new FlushCache();
    }

    public function testDoExecuteCallsAllDefaultSubCommands()
    {
        $responseMock          = $this
            ->getMockBuilder(IResponse::class)
            ->disableOriginalConstructor()
            ->getMock();
        $commandCollectionMock = $this->getMockBuilder(CommandCollection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandCollectionMock->expects($this->exactly(4))->method('call');

        $this->sut->setCommandCollection($commandCollectionMock);
        $this->sut->execute($responseMock);
    }

    public function testDoExecuteCallsExtraSubCommands()
    {
        $responseMock          = $this
            ->getMockBuilder(IResponse::class)
            ->disableOriginalConstructor()
            ->getMock();
        $commandCollectionMock = $this->getMockBuilder(CommandCollection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandCollectionMock->expects($this->exactly(5))->method('call');

        $this->sut->addSubCommand('foo');

        $this->sut->setCommandCollection($commandCollectionMock);
        $this->sut->execute($responseMock);
    }

    public function testDoExecuteWritesErrorMessageOnException()
    {
        $ex = new \Exception();

        $responseMock          = $this
            ->getMockBuilder(IResponse::class)
            ->disableOriginalConstructor()
            ->getMock();
        $commandCollectionMock = $this->getMockBuilder(CommandCollection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock->expects($this->exactly(8))->method('writeln');

        $commandCollectionMock->expects($this->exactly(4))->method('call')->willThrowException($ex);

        $this->sut->setCommandCollection($commandCollectionMock);
        $this->sut->execute($responseMock);
    }
}
