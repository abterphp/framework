<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Grid\Component\Actions;
use AbterPhp\Framework\Grid\Component\Filters;
use AbterPhp\Framework\Grid\Pagination\IPagination;
use AbterPhp\Framework\Grid\Pagination\Pagination;
use AbterPhp\Framework\Grid\Table\Table;
use PHPUnit\Framework\MockObject\MockObject;

class GridTest extends \PHPUnit\Framework\TestCase
{
    public function testToStringContainsTable()
    {
        /** @var Table|MockObject $table */
        $table = $this->getMockBuilder(Table::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMock();

        $table->expects($this->any())->method('__toString')->willReturn('!A!');

        $sut = new Grid($table);

        $this->assertContains('!A!', (string)$sut);
    }

    public function testToStringContainsPagination()
    {
        /** @var Table|MockObject $table */
        $table = $this->getMockBuilder(Table::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMock();

        /** @var Pagination|MockObject $pagination */
        $pagination = $this->getMockBuilder(Pagination::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMock();

        $table->expects($this->any())->method('__toString')->willReturn('!A!');
        $pagination->expects($this->any())->method('__toString')->willReturn('!B!');

        $sut = new Grid($table, $pagination);

        $this->assertContains('!B!', (string)$sut);
    }

    public function testToStringContainsFilters()
    {
        /** @var Table|MockObject $table */
        $table = $this->getMockBuilder(Table::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMock();

        /** @var Filters|MockObject $filters */
        $filters = $this->getMockBuilder(Filters::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMock();

        $table->expects($this->any())->method('__toString')->willReturn('!A!');
        $filters->expects($this->any())->method('__toString')->willReturn('!C!');

        $sut = new Grid($table, null, $filters);

        $this->assertContains('!C!', (string)$sut);
    }

    public function testToStringContainsActions()
    {
        /** @var Table|MockObject $table */
        $table = $this->getMockBuilder(Table::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMock();

        /** @var Actions|MockObject $actions */
        $actions = $this->getMockBuilder(Actions::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString', 'appendToAttribute'])
            ->getMock();

        $table->expects($this->any())->method('__toString')->willReturn('!A!');
        $actions->expects($this->any())->method('__toString')->willReturn('!D!');

        $sut = new Grid($table, null, null, $actions);

        $this->assertContains('!D!', (string)$sut);
    }

    public function testToStringCanWrapContentInForm()
    {
        /** @var Table|MockObject $table */
        $table = $this->getMockBuilder(Table::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMock();

        $table->expects($this->any())->method('__toString')->willReturn('A');

        $sut = new Grid($table);

        $this->assertContains(Grid::TAG_GRID, (string)$sut);
    }

    public function testSetTemplateChangesToString()
    {
        $template = '--||--';

        /** @var Table|MockObject $table */
        $table = $this->getMockBuilder(Table::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMock();

        $table->expects($this->any())->method('__toString')->willReturn('A');

        $sut = new Grid($table);

        $sut->setTemplate($template);

        $this->assertContains($template, (string)$sut);
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetPageThrowsExceptionIfPaginationIsMissing()
    {
        /** @var Table|MockObject $table */
        $table = $this->getMockBuilder(Table::class)
            ->disableOriginalConstructor()
            ->getMock();

        $sut = new Grid($table);

        $sut->getPageSize();
    }

    public function testGetPageSizeCallsPagination()
    {
        $pageSize = 438;

        /** @var Table|MockObject $table */
        $table = $this->getMockBuilder(Table::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMock();

        /** @var Pagination|MockObject $pagination */
        $pagination = $this->getMockBuilder(Pagination::class)
            ->disableOriginalConstructor()
            ->setMethods(['getPageSize'])
            ->getMock();

        $pagination->expects($this->once())->method('getPageSize')->willReturn($pageSize);

        $sut = new Grid($table, $pagination);

        $actualResult = $sut->getPageSize();

        $this->assertSame($pageSize, $actualResult);
    }

    public function testGetSortConditionsCallsTable()
    {
        $sortConditions = ['foo' => 'bar'];

        /** @var Table|MockObject $table */
        $table = $this->getMockBuilder(Table::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSortConditions'])
            ->getMock();
        $table->expects($this->once())->method('getSortConditions')->willReturn($sortConditions);

        $sut = new Grid($table);

        $actualResult = $sut->getSortConditions();

        $this->assertSame($sortConditions, $actualResult);
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetWhereConditionsThrowsExceptionIfFiltersIsMissing()
    {
        /** @var Table|MockObject $table */
        $table = $this->getMockBuilder(Table::class)
            ->disableOriginalConstructor()
            ->getMock();

        $sut = new Grid($table);

        $sut->getWhereConditions();
    }

    public function testGetWhereConditionsCallsFilters()
    {
        $whereConditions = ['foo' => 'bar'];

        /** @var Table|MockObject $table */
        $table = $this->getMockBuilder(Table::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMock();

        /** @var Filters|MockObject $filters */
        $filters = $this->getMockBuilder(Filters::class)
            ->disableOriginalConstructor()
            ->setMethods(['getWhereConditions'])
            ->getMock();
        $filters->expects($this->once())->method('getWhereConditions')->willReturn($whereConditions);

        $sut = new Grid($table, null, $filters);

        $actualResult = $sut->getWhereConditions();

        $this->assertSame($whereConditions, $actualResult);
    }

    /**
     * @expectedException \LogicException
     */
    public function testGetSqlParamsThrowsExceptionIfFiltersIsMissing()
    {
        /** @var Table|MockObject $table */
        $table = $this->getMockBuilder(Table::class)
            ->disableOriginalConstructor()
            ->getMock();

        $sut = new Grid($table);

        $sut->getSqlParams();
    }

    public function testGetSqlParamsCallsFilters()
    {
        $tableSqlParams = ['foo' => 'bar'];
        $filterSqlParams = ['bar' => 'baz'];

        /** @var Table|MockObject $table */
        $table = $this->getMockBuilder(Table::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSqlParams'])
            ->getMock();

        /** @var Filters|MockObject $filters */
        $filters = $this->getMockBuilder(Filters::class)
            ->disableOriginalConstructor()
            ->setMethods(['getSqlParams'])
            ->getMock();

        $table->expects($this->once())->method('getSqlParams')->willReturn($tableSqlParams);
        $filters->expects($this->once())->method('getSqlParams')->willReturn($filterSqlParams);

        $sut = new Grid($table, null, $filters);

        $actualResult = $sut->getSqlParams();

        foreach ($tableSqlParams as $key => $param) {
            $this->assertArrayHasKey($key, $actualResult);
            $this->assertContains($param, $actualResult);
        }
        foreach ($filterSqlParams as $key => $param) {
            $this->assertArrayHasKey($key, $actualResult);
            $this->assertContains($param, $actualResult);
        }
    }

    /**
     * @expectedException \LogicException
     */
    public function testSetTotalCountThrowsExceptionIfPaginationIsMissing()
    {
        /** @var Table|MockObject $table */
        $table = $this->getMockBuilder(Table::class)
            ->disableOriginalConstructor()
            ->getMock();

        $sut = new Grid($table);

        $sut->setTotalCount(123);
    }

    public function testGetTotalCountCallsPagination()
    {
        $totalCount = 10;

        /** @var Table|MockObject $table */
        $table = $this->getMockBuilder(Table::class)
            ->disableOriginalConstructor()
            ->setMethods(['__toString'])
            ->getMock();

        /** @var Pagination|MockObject $pagination */
        $pagination = $this->getMockBuilder(Pagination::class)
            ->disableOriginalConstructor()
            ->setMethods(['setTotalCount'])
            ->getMock();
        $pagination->expects($this->once())->method('setTotalCount')->with($totalCount);

        $sut = new Grid($table, $pagination);

        $sut->setTotalCount($totalCount);
    }

    public function testSetEntitiesCallsTable()
    {
        $entity = $this->getMockBuilder(IStringerEntity::class)
            ->setMethods(['__toString', 'getId', 'setId', 'toJSON'])
            ->getMock();

        /** @var Table|MockObject $table */
        $table = $this->getMockBuilder(Table::class)
            ->disableOriginalConstructor()
            ->setMethods(['setEntities'])
            ->getMock();
        $table->expects($this->once())->method('setEntities')->with([$entity]);

        $sut = new Grid($table);

        $sut->setEntities([$entity]);
    }

    public function getExtendNodesProvider(): array
    {
        /** @var Table|MockObject $tableStub */
        $tableStub = $this->getMockBuilder(Table::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        /** @var Filters|MockObject $filtersStub */
        $filtersStub = $this->getMockBuilder(Filters::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        /** @var IPagination|MockObject $paginationStub */
        $paginationStub = $this->getMockBuilder(IPagination::class)
            ->setMethods([])
            ->getMock();

        return [
            'nay-filters-nay-pagination' => [$tableStub, null, null, [$tableStub]],
            'nay-filters-yay-pagination' => [$tableStub, null, $paginationStub, [$paginationStub, $tableStub]],
            'yay-filters-nay-pagination' => [$tableStub, $filtersStub, null, [$filtersStub, $tableStub]],
            'yay-filters-yay-pagination' => [
                $tableStub,
                $filtersStub,
                $paginationStub,
                [$filtersStub, $paginationStub, $tableStub],
            ],
        ];
    }

    /**
     * @dataProvider getExtendNodesProvider
     *
     * @param Table|MockObject $tableStub
     * @param Filters|null     $filters
     * @param IPagination|null $pagination
     * @param array            $expectedResult
     */
    public function testGetExtendedNodes($tableStub, ?Filters $filters, ?IPagination $pagination, array $expectedResult)
    {
        $sut = new Grid($tableStub, $pagination, $filters);

        $actualResult = $sut->getExtendedNodes();

        $this->assertSame($expectedResult, $actualResult);
    }
}
