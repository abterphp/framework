<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use PHPUnit\Framework\TestCase;

class ContentlessTest extends TestCase
{
    public function testToStringIsEmptyByDefault(): void
    {
        $sut = $this->createContentless();

        $this->assertStringContainsString('', (string)$sut);
    }

    public function testSetContentThrowsExceptionIfCalledWithNotNull(): void
    {
        $this->expectException(\LogicException::class);

        $sut = $this->createContentless();

        $sut->setContent(12);
    }

    public function testSetContentDoesNotThrowExceptionIfCalledWithNull(): void
    {
        $sut = $this->createContentless();

        $actual = $sut->setContent(null);

        $this->assertSame($sut, $actual);
    }

    /**
     * @return Contentless
     */
    protected function createContentless(): INode
    {
        return new Contentless();
    }
}
