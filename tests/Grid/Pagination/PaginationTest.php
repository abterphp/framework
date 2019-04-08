<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Pagination;

use PHPUnit\Framework\TestCase;

class PaginationTest extends TestCase
{
    /**
     * @return array
     */
    public function getTestToStringDataProvider()
    {
        return [
            [1, 10, 8, 5, ['>1<']],
            [1, 10, 12, 5, ['>1<', '>2<']],
            [2, 10, 38, 5, ['>1<', '>2<', '>3<', '>4<']],
            [3, 10, 38, 3, ['>3<', '>4<']],
            [14, 20, 942, 5, ['>12<', '>13<', '>14<', '>15<', '>16<']],
            [200, 20, 5000, 9, ['>196<', '>197<', '>200<', '>203<', '>204<']],
        ];
    }

    /**
     * @dataProvider getTestToStringDataProvider
     *
     * @param int      $page
     * @param int      $pageSize
     * @param int      $totalCount
     * @param int      $numberCount
     * @param string[] $expectedResult
     */
    public function testToString(int $page, int $pageSize, int $totalCount, int $numberCount, array $expectedResult)
    {
        $params = ['page' => (string)$page];

        $sut = new Pagination($params, '', $numberCount, $pageSize, [$pageSize], [], []);

        $sut->setTotalCount($totalCount);

        $actualResult = (string)$sut;

        foreach ($expectedResult as $number) {
            $this->assertContains("$number", $actualResult);
        }

        $this->assertSame($actualResult, (string)$sut);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructFailureOnEvenNumberCount()
    {
        $params = ['page' => '1'];

        new Pagination($params, '', 4, 10, [10], [], []);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructFailureOnInvalidPageSize()
    {
        $params = ['page' => '1'];

        new Pagination($params, '', 5, 8, [10], [], []);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testConstructFailureOnInvalidPageSizeParam()
    {
        $params = ['page' => '1', 'page-size' => -8];

        new Pagination($params, '', 4, 10, [10], [], []);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetTotalCountFailureOnNegativeTotalCount()
    {
        $params = ['page' => '1'];

        $sut = new Pagination($params, '', 5, 10, [10], [], []);

        $sut->setTotalCount(-1);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetTotalCountFailureOutOfRangeTotalCount()
    {
        $params = ['page' => '2'];

        $sut = new Pagination($params, '', 5, 10, [10], [], []);

        $sut->setTotalCount(0);
    }
}
