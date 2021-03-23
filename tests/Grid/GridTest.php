<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid;

use AbterPhp\Framework\Domain\Entities\IStringerEntity;
use AbterPhp\Framework\Grid\Component\Actions;
use AbterPhp\Framework\Grid\Component\Filters;
use AbterPhp\Framework\Grid\Pagination\IPagination;
use AbterPhp\Framework\Grid\Pagination\Pagination;
use AbterPhp\Framework\Grid\Table\Table;
use LogicException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class GridTest extends TestCase
{
    public function testToStringContainsTable()
    {
        /** @var Table|MockObject $table */
        $table = $this->createMock(Table::class);

        $table->expects($this->any())->method('__toString')->willReturn('!A!');

        $sut = new Grid($table);

        $this->assertStringContainsString('!A!', (string)$sut);
    }

    public function testToStringContainsPagination()
    {
        /** @var Table|MockObject $table */
        $table = $this->createMock(Table::class);

        /** @var Pagination|MockObject $pagination */
        $pagination = $this->createMock(Pagination::class);

        $table->expects($this->any())->method('__toString')->willReturn('!A!');
        $pagination->expects($this->any())->method('__toString')->willReturn('!B!');

        $sut = new Grid($table, $pagination);

        $this->assertStringContainsString('!B!', (string)$sut);
    }

    public function testToStringContainsFilters()
    {
        /** @var Table|MockObject $table */
        $table = $this->createMock(Table::class);

        /** @var Filters|MockObject $filters */
        $filters = $this->createMock(Filters::class);

        $table->expects($this->any())->method('__toString')->willReturn('!A!');
        $filters->expects($this->any())->method('__toString')->willReturn('!C!');

        $sut = new Grid($table, null, $filters);

        $this->assertStringContainsString('!C!', (string)$sut);
    }

    public function testToStringContainsActions()
    {
        /** @var Table|MockObject $table */
        $table = $this->createMock(Table::class);

        /** @var Actions|MockObject $actions */
        $actions = $this->createMock(Actions::class);

        $table->expects($this->any())->method('__toString')->willReturn('!A!');
        $actions->expects($this->any())->method('__toString')->willReturn('!D!');

        $sut = new Grid($table, null, null, $actions);

        $this->assertStringContainsString('!D!', (string)$sut);
    }

    public function testToStringCanWrapContentInForm()
    {
        /** @var Table|MockObject $table */
        $table = $this->createMock(Table::class);

        $table->expects($this->any())->method('__toString')->willReturn('A');

        $sut = new Grid($table);

        $this->assertStringContainsString('div', (string)$sut);
    }

    public function testSetTemplateChangesToString()
    {
        $template = '--||--';

        /** @var Table|MockObject $table */
        $table = $this->createMock(Table::class);

        $table->expects($this->any())->method('__toString')->willReturn('A');

        $sut = new Grid($table);

        $sut->setTemplate($template);

        $this->assertStringContainsString($template, (string)$sut);
    }

    public function testGetPageThrowsExceptionIfPaginationIsMissing()
    {
        $this->expectException(LogicException::class);

        /** @var Table|MockObject $table */
        $table = $this->createMock(Table::class);

        $sut = new Grid($table);

        $sut->getPageSize();
    }

    public function testGetPageSizeCallsPagination()
    {
        $pageSize = 438;

        /** @var Table|MockObject $table */
        $table = $this->createMock(Table::class);

        /** @var Pagination|MockObject $pagination */
        $pagination = $this->createMock(Pagination::class);

        $pagination->expects($this->once())->method('getPageSize')->willReturn($pageSize);

        $sut = new Grid($table, $pagination);

        $actualResult = $sut->getPageSize();

        $this->assertSame($pageSize, $actualResult);
    }

    public function testGetSortConditionsCallsTable()
    {
        $sortConditions = ['foo' => 'bar'];

        /** @var Table|MockObject $table */
        $table = $this->createMock(Table::class);
        $table->expects($this->once())->method('getSortConditions')->willReturn($sortConditions);

        $sut = new Grid($table);

        $actualResult = $sut->getSortConditions();

        $this->assertSame($sortConditions, $actualResult);
    }

    public function testGetWhereConditionsThrowsExceptionIfFiltersIsMissing()
    {
        $this->expectException(LogicException::class);

        /** @var Table|MockObject $table */
        $table = $this->createMock(Table::class);

        $sut = new Grid($table);

        $sut->getWhereConditions();
    }

    public function testGetWhereConditionsCallsFilters()
    {
        $whereConditions = ['foo' => 'bar'];

        /** @var Table|MockObject $table */
        $table = $this->createMock(Table::class);

        /** @var Filters|MockObject $filters */
        $filters = $this->createMock(Filters::class);
        $filters->expects($this->once())->method('getWhereConditions')->willReturn($whereConditions);

        $sut = new Grid($table, null, $filters);

        $actualResult = $sut->getWhereConditions();

        $this->assertSame($whereConditions, $actualResult);
    }

    public function testGetSqlParamsThrowsExceptionIfFiltersIsMissing()
    {
        $this->expectException(LogicException::class);

        /** @var Table|MockObject $table */
        $table = $this->createMock(Table::class);

        $sut = new Grid($table);

        $sut->getSqlParams();
    }

    public function testGetSqlParamsCallsFilters()
    {
        $tableSqlParams = ['foo' => 'bar'];
        $filterSqlParams = ['bar' => 'baz'];

        /** @var Table|MockObject $table */
        $table = $this->createMock(Table::class);

        /** @var Filters|MockObject $filters */
        $filters = $this->createMock(Filters::class);

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

    public function testSetTotalCountThrowsExceptionIfPaginationIsMissing()
    {
        $this->expectException(LogicException::class);

        /** @var Table|MockObject $table */
        $table = $this->createMock(Table::class);

        $sut = new Grid($table);

        $sut->setTotalCount(123);
    }

    public function testGetTotalCountCallsPagination()
    {
        $totalCount = 10;

        /** @var Table|MockObject $table */
        $table = $this->createMock(Table::class);

        /** @var Pagination|MockObject $pagination */
        $pagination = $this->createMock(Pagination::class);
        $pagination->expects($this->once())->method('setTotalCount')->with($totalCount);

        $sut = new Grid($table, $pagination);

        $sut->setTotalCount($totalCount);
    }

    public function testSetEntitiesCallsTable()
    {
        $entity = $this->createMock(IStringerEntity::class);

        /** @var Table|MockObject $table */
        $table = $this->createMock(Table::class);
        $table->expects($this->once())->method('setEntities')->with([$entity]);

        $sut = new Grid($table);

        $sut->setEntities([$entity]);
    }

    public function getExtendNodesProvider(): array
    {
        /** @var Table|MockObject $tableStub */
        $tableStub = $this->createMock(Table::class);

        /** @var Filters|MockObject $filtersStub */
        $filtersStub = $this->createMock(Filters::class);

        /** @var IPagination|MockObject $paginationStub */
        $paginationStub = $this->createMock(IPagination::class);

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
