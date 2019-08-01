<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Navigation\Navigation;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class NavigationReadyTest extends TestCase
{
    /** @var Navigation|MockObject */
    protected $navigationMock;

    /** @var NavigationReady */
    protected $sut;

    public function setUp(): void
    {
        $this->navigationMock = $this->getMockBuilder(Navigation::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->sut = new NavigationReady($this->navigationMock);

        parent::setUp();
    }

    public function testGetNavigation()
    {
        $actualResult = $this->sut->getNavigation();

        $this->assertSame($this->navigationMock, $actualResult);
    }
}
