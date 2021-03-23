<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Navigation\Navigation;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NavigationReadyTest extends TestCase
{
    /** @var NavigationReady - System Under Test */
    protected NavigationReady $sut;

    /** @var Navigation|MockObject */
    protected $navigationMock;

    public function setUp(): void
    {
        $this->navigationMock = $this->createMock(Navigation::class);

        $this->sut = new NavigationReady($this->navigationMock);

        parent::setUp();
    }

    public function testGetNavigation()
    {
        $actualResult = $this->sut->getNavigation();

        $this->assertSame($this->navigationMock, $actualResult);
    }
}
