<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Grid\Grid;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GridReadyTest extends TestCase
{
    /** @var GridReady - System Under Test */
    protected GridReady $sut;

    /** @var Grid|MockObject */
    protected $gridMock;

    public function setUp(): void
    {
        $this->gridMock = $this->createMock(Grid::class);

        $this->sut = new GridReady($this->gridMock);

        parent::setUp();
    }

    public function testGetGrid()
    {
        $actualResult = $this->sut->getGrid();

        $this->assertSame($this->gridMock, $actualResult);
    }
}
