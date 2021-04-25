<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Component;

use AbterPhp\Framework\Grid\Action\Action;
use PHPUnit\Framework\TestCase;

class ActionsTest extends TestCase
{
    public function testCloneWithoutNodes(): void
    {
        $sut = new Actions();

        $clone = clone $sut;

        $this->assertNotSame($sut, $clone);
        $this->assertEquals($sut, $clone);
    }

    public function testCloneWithNodes(): void
    {
        $sut = new Actions();

        $sut[] = new Action('abc');
        $sut[] = new Action('bcd');

        $clone = clone $sut;

        $this->assertCount(2, $clone->getNodes());
        $this->assertNotSame($sut, $clone);
        $this->assertEquals($sut, $clone);
    }
}
