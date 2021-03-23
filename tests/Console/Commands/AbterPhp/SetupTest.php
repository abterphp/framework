<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Console\Commands\AbterPhp;

use Opulence\Console\Commands\CommandCollection;
use Opulence\Console\Responses\IResponse;
use PHPUnit\Framework\TestCase;

class SetupTest extends TestCase
{
    /** @var Setup - System Under Test */
    protected Setup $sut;

    public function setUp(): void
    {
        $this->sut = new Setup();
    }

    public function testExecuteCallsDefaultSubCommand()
    {
        $responseMock = $this->getMockBuilder(IResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandCollectionMock = $this->getMockBuilder(CommandCollection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandCollectionMock->expects($this->once())->method('call');

        $this->sut->setCommandCollection($commandCollectionMock);

        $this->sut->execute($responseMock);
    }

    public function testExecuteCallsAdditionalSubCommands()
    {
        $responseMock = $this->getMockBuilder(IResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandCollectionMock = $this->getMockBuilder(CommandCollection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandCollectionMock->expects($this->exactly(2))->method('call');

        $this->sut->addSubCommand('foo');

        $this->sut->setCommandCollection($commandCollectionMock);

        $this->sut->execute($responseMock);
    }

    public function testExecutesWritesResponseOnExceptions()
    {
        $ex = new \RuntimeException('foo');

        $responseMock = $this->getMockBuilder(IResponse::class)
            ->disableOriginalConstructor()
            ->getMock();

        $responseMock->expects($this->atLeastOnce())->method('writeln');

        $commandCollectionMock = $this->getMockBuilder(CommandCollection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $commandCollectionMock->expects($this->any())->method('call')->willThrowException($ex);

        $this->sut->setCommandCollection($commandCollectionMock);

        $this->sut->execute($responseMock);
    }
}
