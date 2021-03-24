<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Events;

use AbterPhp\Framework\Authorization\CombinedAdapter;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class AuthReadyTest extends TestCase
{
    /** @var AuthReady - System Under Test */
    protected AuthReady $sut;

    /** @var CombinedAdapter|MockObject */
    protected $adapterMock;

    public function setUp(): void
    {
        $this->adapterMock = $this->createMock(CombinedAdapter::class);

        $this->sut = new AuthReady($this->adapterMock);

        parent::setUp();
    }

    public function testGetAdapter(): void
    {
        $actualResult = $this->sut->getAdapter();

        $this->assertSame($this->adapterMock, $actualResult);
    }
}
