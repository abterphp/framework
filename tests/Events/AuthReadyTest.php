<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Authorization\CombinedAdapter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AuthReadyTest extends TestCase
{
    /** @var CombinedAdapter|MockObject */
    protected $adapterMock;

    /** @var AuthReady */
    protected $sut;

    public function setUp(): void
    {
        $this->adapterMock = $this->getMockBuilder(CombinedAdapter::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $this->sut = new AuthReady($this->adapterMock);

        parent::setUp();
    }

    public function testGetAdapter()
    {
        $actualResult = $this->sut->getAdapter();

        $this->assertSame($this->adapterMock, $actualResult);
    }
}
