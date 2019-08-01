<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Databases\Migrations;

use AbterPhp\Framework\Filesystem\FileFinder;
use DateTime;
use Opulence\Databases\Adapters\Pdo\Connection;
use Opulence\Databases\Adapters\Pdo\Statement;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class BaseMigrationTest extends TestCase
{
    /** @var BaseMigration - System Under Test */
    protected $sut;

    /** @var Connection|MockObject */
    protected $connectionMock;

    /** @var FileFinder|MockObject */
    protected $fileFinderMock;

    /** @var string */
    protected $migrationsPath = 'foo';

    /** @var string */
    protected $driverName = 'bar';

    public function setUp(): void
    {
        $this->connectionMock = $this->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->setMethods(['prepare'])
            ->getMock();

        $this->fileFinderMock = $this->getMockBuilder(FileFinder::class)
            ->disableOriginalConstructor()
            ->setMethods(['registerFilesystem', 'has', 'read'])
            ->getMock();

        $this->sut = new BaseMigration($this->connectionMock, $this->fileFinderMock);

        parent::setUp();
    }

    public function testGetCreationDateCreatesADateTime()
    {
        $actualResult = $this->sut->getCreationDate();

        $this->assertInstanceOf(DateTime::class, $actualResult);

        $diff = (new DateTime())->diff($actualResult);
        $this->assertSame('0,0,0,0,0', $diff->format('%y,%m,%d,%h,%m'));
    }

    public function testDownLoadsAFileAndExecutesItAsSql()
    {
        $content = 'SELECT 1;';

        $statementMock = $this->getMockBuilder(Statement::class)
            ->disableOriginalConstructor()
            ->setMethods(['execute'])
            ->getMock();
        $statementMock->expects($this->once())->method('execute');

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

    public function testUpLoadsAFileAndExecutesItAsSql()
    {
        $content = 'SELECT 1;';

        $statementMock = $this->getMockBuilder(Statement::class)
            ->disableOriginalConstructor()
            ->setMethods(['execute'])
            ->getMock();
        $statementMock->expects($this->once())->method('execute');

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
