<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Databases\Migrations;

use AbterPhp\Framework\Filesystem\FileFinder;
use DateTime;
use Opulence\Databases\Adapters\Pdo\Connection;
use Opulence\Databases\Adapters\Pdo\Statement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BaseMigrationsTest extends TestCase
{
    /** @var BaseMigration - System Under Test */
    protected BaseMigration $sut;

    /** @var Connection|MockObject */
    protected $connectionMock;

    /** @var FileFinder|MockObject */
    protected $fileFinderMock;

    public function setUp(): void
    {
        $this->connectionMock = $this->createMock(Connection::class);

        $this->fileFinderMock = $this->createMock(FileFinder::class);

        $this->sut = new BaseMigration($this->connectionMock, $this->fileFinderMock);

        parent::setUp();
    }

    public function testGetCreationDateCreatesADateTime(): void
    {
        $actualResult = $this->sut->getCreationDate();

        $this->assertInstanceOf(DateTime::class, $actualResult);

        $diff = (new DateTime())->diff($actualResult);
        $this->assertSame('0,0,0,0,0', $diff->format('%y,%m,%d,%h,%m'));
    }

    public function testDownLoadsAFileAndExecutesItAsSql(): void
    {
        $content = 'SELECT 1;';

        $statementMock = $this->createMock(Statement::class);
        $statementMock->expects($this->once())->method('execute')->willReturn(true);

        $this->fileFinderMock
            ->expects($this->any())
            ->method('read')
            ->with('down/foo-bar')
            ->willReturn($content);

        $this->connectionMock
            ->expects($this->any())
            ->method('prepare')
            ->with($content)
            ->willReturn($statementMock);

        $this->sut->down();
    }

    public function testUpLoadsAFileAndExecutesItAsSql(): void
    {
        $content = 'SELECT 1;';

        $statementMock = $this->createMock(Statement::class);
        $statementMock->expects($this->once())->method('execute')->willReturn(true);

        $this->fileFinderMock
            ->expects($this->any())
            ->method('read')
            ->with('up/foo-bar')
            ->willReturn($content);

        $this->connectionMock
            ->expects($this->any())
            ->method('prepare')
            ->with($content)
            ->willReturn($statementMock);

        $this->sut->up();
    }

    public function testDownThrowsExceptionIfExecutionFails(): void
    {
        $this->expectException(Exception::class);

        $exceptionData = ['foo', 'bar', 'baz'];

        $content = 'SELECT 1;';

        $statementMock = $this->createMock(Statement::class);
        $statementMock->expects($this->once())->method('execute')->willReturn(false);
        $statementMock->expects($this->atLeastOnce())->method('errorInfo')->willReturn($exceptionData);

        $this->fileFinderMock
            ->expects($this->any())
            ->method('read')
            ->with('down/foo-bar')
            ->willReturn($content);

        $this->connectionMock
            ->expects($this->any())
            ->method('prepare')
            ->with($content)
            ->willReturn($statementMock);

        $this->sut->down();
    }

    public function testUpThrowsExceptionIfExecutionFails(): void
    {
        $this->expectException(Exception::class);

        $exceptionData = ['foo', 'bar', 'baz'];

        $content = 'SELECT 1;';

        $statementMock = $this->createMock(Statement::class);
        $statementMock->expects($this->once())->method('execute')->willReturn(false);
        $statementMock->expects($this->atLeastOnce())->method('errorInfo')->willReturn($exceptionData);

        $this->fileFinderMock
            ->expects($this->any())
            ->method('read')
            ->with('up/foo-bar')
            ->willReturn($content);

        $this->connectionMock
            ->expects($this->any())
            ->method('prepare')
            ->with($content)
            ->willReturn($statementMock);

        $this->sut->up();
    }
}
