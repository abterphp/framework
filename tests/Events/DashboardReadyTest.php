<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Dashboard\Dashboard;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DashboardReadyTest extends TestCase
{
    /** @var Dashboard|MockObject */
    protected $dashboardMock;

    /** @var AuthReady */
    protected $sut;

    public function setUp()
    {
        $this->dashboardMock = $this->getMockBuilder(Dashboard::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->sut = new DashboardReady($this->dashboardMock);
    }

    public function testGetDashboard()
    {
        $actualResult = $this->sut->getDashboard();

        $this->assertSame($this->dashboardMock, $actualResult);
    }
}
