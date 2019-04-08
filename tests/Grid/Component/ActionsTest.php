<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Component;

use AbterPhp\Framework\Grid\Action\Action;

class ActionsTest extends \PHPUnit\Framework\TestCase
{
    public function testDuplicateWithEmptyNodes()
    {
        $sut = new Actions();

        $duplicate = $sut->duplicate();

        $this->assertNotSame($sut, $duplicate);
        $this->assertEquals($sut, $duplicate);
    }

    public function testDuplicateWithNodes()
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
