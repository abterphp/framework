<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Factory;

use AbterPhp\Framework\Grid\Component\Filters;
use AbterPhp\Framework\Grid\Grid;
use AbterPhp\Framework\Grid\Pagination\Pagination;
use AbterPhp\Framework\Grid\Table\Table;
use PHPUnit\Framework\MockObject\MockObject;

class GridFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testCreate()
    {
        $sut = new GridFactory();

        /** @var Table|MockObject $tableMock */
        $tableMock = $this->getMockBuilder(Table::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMock();

        /** @var Pagination|MockObject $paginationMock */
        $paginationMock = $this->getMockBuilder(Pagination::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMock();

        /** @var Filters|MockObject $filtersMock */
        $filtersMock = $this->getMockBuilder(Filters::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMock();

        $grid = $sut->create($tableMock, $paginationMock, $filtersMock, null);

        $this->assertInstanceOf(Grid::class, $grid);
    }
}
