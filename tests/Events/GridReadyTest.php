<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Grid\Grid;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GridReadyTest extends TestCase
{
    /** @var Grid|MockObject */
    protected $gridMock;

    /** @var NavigationReady */
    protected $sut;

    public function setUp(): void
    {
        $this->gridMock = $this->getMockBuilder(Grid::class)
            ->disableOriginalConstructor()
            ->setMethods([])
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
