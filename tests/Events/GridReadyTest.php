<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Grid\Grid;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GridReadyTest extends TestCase
{
    /** @var NavigationReady - System Under Test */
    protected $sut;

    /** @var Grid|MockObject */
    protected $gridMock;

    public function setUp(): void
    {
        $this->gridMock = $this->getMockBuilder(Grid::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $this->sut = new GridReady($this->gridMock);

        parent::setUp();
    }

    public function testGetGrid()
    {
        $actualResult = $this->sut->getGrid();

        $this->assertSame($this->gridMock, $actualResult);
    }
}
