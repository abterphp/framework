<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Dashboard\Dashboard;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DashboardReadyTest extends TestCase
{
    /** @var DashboardReady - System Under Test */
    protected DashboardReady $sut;

    /** @var Dashboard|MockObject */
    protected $dashboardMock;

    public function setUp(): void
    {
        $this->dashboardMock = $this->createMock(Dashboard::class);

        $this->sut = new DashboardReady($this->dashboardMock);

        parent::setUp();
    }

    public function testGetDashboard()
    {
        $actualResult = $this->sut->getDashboard();

        $this->assertSame($this->dashboardMock, $actualResult);
    }
}
