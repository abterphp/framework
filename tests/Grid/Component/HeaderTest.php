<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Component;

use AbterPhp\Framework\Grid\Cell\Cell;
use AbterPhp\Framework\Grid\Cell\Sortable;
use AbterPhp\Framework\Grid\Collection\Cells;
use AbterPhp\Framework\Grid\Row\IRow;
use AbterPhp\Framework\Grid\Row\Row;
use PHPUnit\Framework\TestCase;

class HeaderTest extends TestCase
{
    public function testSetBaseUrlSetsBaseUrlOfAllSortables(): void
    {
        $expectedResult = '/foo';

        $sut = $this->createHeader([], [], ['a' => 'A', 'b' => 'B'], ['c' => 'A', 'd' => 'B']);

        $sut->setBaseUrl($expectedResult);

        foreach ($sut as $row) {
            assert($row instanceof IRow);

            foreach ($row->getCells() as $cell) {
                if ($cell instanceof Sortable) {
                    $this->assertSame($expectedResult, $cell->getBaseUrl());
                } else {
                    $this->fail('$cell is not Sortable');
                }
            }
        }
    }

    public function testSetBaseUrlSkipsNonSortables(): void
    {
        $expectedResult = '/foo';

        $sut = $this->createHeader([], [], ['a' => 'A', 'b' => 'B'], ['c' => 'A', 'd' => 'B']);

        $cells   = new Cells();
        $cells[] = new Cell('e', 'A');
        $cells[] = new Cell('f', 'B');
        $sut[]   = new Row($cells);

        $sut->setBaseUrl($expectedResult);

        foreach ($sut as $row) {
            assert($row instanceof IRow);
            foreach ($row->getCells() as $cell) {
                if (!($cell instanceof Sortable)) {
                    continue;
                }

                $this->assertSame($expectedResult, $cell->getBaseUrl());
            }
        }
    }

    public function testSetParamsSkipsNonSortables(): void
    {
        $params = [];

        $sut = $this->createHeader([], [], ['a' => 'A', 'b' => 'B'], ['c' => 'A', 'd' => 'B']);

        $cells   = new Cells();
        $cells[] = new Cell('e', 'A');
        $cells[] = new Cell('f', 'B');
        $sut[]   = new Row($cells);

        $sut->setParams($params);

        foreach ($sut as $row) {
            assert($row instanceof IRow);

            foreach ($row->getCells() as $cell) {
                if (!($cell instanceof Sortable)) {
                    continue;
                }

                $this->assertNull($cell->getQueryParam());
            }
        }
    }

    /**
     * @return array[]
     */
    public function getSortedUrlProvider(): array
    {
        return [
            'no-params' => [
                [],
                '/foo?',
                '/foo?',
            ],
            'non-matching-params' => [
                ['bar' => '1'],
                '/foo?',
                '/foo?',
            ],
            'matching-param-zero-value-is-ignored' => [
                ['sort-A-input' => '0'],
                '/foo?',
                '/foo?',
            ],
            'one-matching-param-positive' => [
                ['sort-A-input' => '1'],
                '/foo?',
                '/foo?sort-A-input=1&',
            ],
            'one-matching-param-very-positive' => [
                ['sort-A-input' => '100'],
                '/foo?',
                '/foo?sort-A-input=100&',
            ],
            'one-matching-param-negative' => [
                ['sort-A-input' => '-1'],
                '/foo?',
                '/foo?sort-A-input=-1&',
            ],
            'one-matching-param-very-negative' => [
                ['sort-A-input' => '-100'],
                '/foo?',
                '/foo?sort-A-input=-100&',
            ],
            'matching-param-one-matching-one-ignored' => [
                ['sort-A-input' => '1', 'sort-B-input' => '0'],
                '/foo?',
                '/foo?sort-A-input=1&',
            ],
            'complex' => [
                ['sort-A-input' => '1', 'sort-B-input' => '-1'],
                '/foo?',
                '/foo?sort-A-input=1&sort-B-input=-1&',
            ],
        ]   ;
    }

    /**
     * @dataProvider getSortedUrlProvider
     *
     * @param array  $params
     * @param string $baseUrl
     * @param string $expectedResult
     */
    public function testGetSortedUrl(array $params, string $baseUrl, string $expectedResult): void
    {
        $sut = $this->createHeader(
            ['A' => 'A-input', 'B' => 'B-input'],
            ['A' => 'a_field', 'b' => 'b_field'],
            ['a' => 'A', 'b' => 'B']
        )  ;

        $sut->setParams($params);

        $actualResult = $sut->getSortedUrl($baseUrl);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testGetSortedUrlSkipsNonSortables(): void
    {
        $baseUrl = '/foo?';
        $params = [];

        $sut = $this->createHeader([], [], ['a' => 'A', 'b' => 'B'], ['c' => 'A', 'd' => 'B']);

        $cells   = new Cells();
        $cells[] = new Cell('e', 'A');
        $cells[] = new Cell('f', 'B');
        $sut[]   = new Row($cells);

        $sut->setParams($params);

        $actualResult = $sut->getSortedUrl($baseUrl);

        $this->assertSame($baseUrl, $actualResult);
    }

    public function testGetSortedConditions(): void
    {
        $expectedResult = [
            'a_field ASC',
            'b_field DESC',
        ];

        $params = [
            'sort-A-input' => '1',
            'sort-B-input' => '-1',
        ];

        $sut = $this->createHeader(
            ['A' => 'A-input', 'B' => 'B-input'],
            ['A' => 'a_field', 'B' => 'b_field'],
            ['a' => 'A', 'b' => 'B']
        )  ;

        $sut->setParams($params);

        $actualResult = $sut->getSortConditions();

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testGetSortConditionSkipsNonSortables(): void
    {
        $params = [];

        $sut = $this->createHeader([], [], ['a' => 'A', 'b' => 'B'], ['c' => 'A', 'd' => 'B']);

        $cells   = new Cells();
        $cells[] = new Cell('e', 'A');
        $cells[] = new Cell('f', 'B');
        $sut[]   = new Row($cells);

        $sut->setParams($params);

        $actualResult = $sut->getSortConditions();

        $this->assertSame([], $actualResult);
    }

    /**
     * @param array $inputNames
     * @param array $fieldNames
     * @param array ...$rows
     *
     * @return Header
     */
    private function createHeader(array $inputNames, array $fieldNames, array ...$rows): Header
    {
        $sut = new Header();

        foreach ($rows as $row) {
            $cells = new Cells();
            foreach ($row as $content => $group) {
                $inputName = $inputNames[$group] ?? '';
                $fieldName = $fieldNames[$group] ?? '';

                $cells[] = new Sortable($content, $group, $inputName, $fieldName);
            }
            $sut[] = new Row($cells);
        }

        return $sut;
    }

    public function testGetQueryParams(): void
    {
        $sut = $this->createHeader([], [], ['a' => 'A', 'b' => 'B'], ['c' => 'A', 'd' => 'B']);

        $actualResult = $sut->getQueryParams();

        $this->assertSame([], $actualResult);
    }
}
