<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Factory\Table;

use AbterPhp\Framework\Grid\Cell\Cell;
use AbterPhp\Framework\Grid\Component\Header;
use AbterPhp\Framework\Grid\Row\Row;

class HeaderFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreateWithoutActions()
    {
        $baseUrl = '/foo?';

        $sut = new HeaderFactory();

        $header = $sut->create(false, [], $baseUrl);

        $this->assertInstanceOf(Header::class, $header);
        $this->assertCount(1, $header);
        $this->assertInstanceOf(Row::class, $header[0]);

        /** @var Row $firstRow */
        $firstRow = $header[0];
        $this->assertCount(0, $firstRow->getCells());
    }

    public function testCreateWithActions()
    {
        $baseUrl = '/foo?';

        $sut = new HeaderFactory();

        $header = $sut->create(true, [], $baseUrl);

        $this->assertInstanceOf(Header::class, $header);
        $this->assertCount(1, $header);
        $this->assertInstanceOf(Row::class, $header[0]);

        /** @var Row $firstRow */
        $firstRow = $header[0];
        $this->assertCount(1, $firstRow->getCells());
        $this->assertInstanceOf(Cell::class, $firstRow->getCells()[0]);
    }

    public function testCreateWithSortable()
    {
        $baseUrl = '/foo?';

        $sut = new HeaderFactory();

        $header = $sut->create(true, [], $baseUrl);

        $this->assertInstanceOf(Header::class, $header);
        $this->assertCount(1, $header);
        $this->assertInstanceOf(Row::class, $header[0]);

        /** @var Row $firstRow */
        $firstRow = $header[0];
        $this->assertCount(1, $firstRow->getCells());
        $this->assertInstanceOf(Cell::class, $firstRow->getCells()[0]);
    }
}
