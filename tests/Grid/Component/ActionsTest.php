<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Component;

use AbterPhp\Framework\Grid\Action\Action;
use PHPUnit\Framework\TestCase;

class ActionsTest extends TestCase
{
    public function testDuplicateWithEmptyNodes(): void
    {
        $sut = new Actions();

        $duplicate = $sut->duplicate();

        $this->assertNotSame($sut, $duplicate);
        $this->assertEquals($sut, $duplicate);
    }

    public function testDuplicateWithNodes(): void
    {
        $sut = new Actions();

        $sut[] = new Action('abc');
        $sut[] = new Action('bcd');

        $duplicate = $sut->duplicate();

        $this->assertCount(2, $duplicate);
        $this->assertNotSame($sut, $duplicate);
        $this->assertEquals($sut, $duplicate);
    }
}
