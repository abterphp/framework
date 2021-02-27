<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Pagination;

use AbterPhp\Framework\Form\Element\Select;
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
            $this->assertStringContainsString("$number", $actualResult);
        }

        $this->assertSame($actualResult, (string)$sut);
    }

    public function testConstructFailureOnEvenNumberCount()
    {
        $this->expectException(\InvalidArgumentException::class);

        $params = ['page' => '1'];

        new Pagination($params, '', 4, 10, [10], [], []);
    }

    public function testConstructFailureOnInvalidPageSize()
    {
        $this->expectException(\InvalidArgumentException::class);

        $params = ['page' => '1'];

        new Pagination($params, '', 5, 8, [10], [], []);
    }

    public function testConstructFailureOnInvalidPageSizeParam()
    {
        $this->expectException(\InvalidArgumentException::class);

        $params = ['page' => '1', 'page-size' => -8];

        new Pagination($params, '', 4, 10, [10], [], []);
    }

    public function testSetTotalCountFailureOnNegativeTotalCount()
    {
        $this->expectException(\InvalidArgumentException::class);

        $params = ['page' => '1'];

        $sut = new Pagination($params, '', 5, 10, [10], [], []);

        $sut->setTotalCount(-1);
    }

    public function testSetTotalCountFailureOutOfRangeTotalCount()
    {
        $this->expectException(\InvalidArgumentException::class);

        $params = ['page' => '2'];

        $sut = new Pagination($params, '', 5, 10, [10], [], []);

        $sut->setTotalCount(0);
    }

    public function testGetNodes()
    {
        $sut = new Pagination([], '', 5, 10, [10], [], []);

        $actualResult = $sut->getNodes();

        $this->assertSame([], $actualResult);
    }

    public function testGetExtendedNodes()
    {
        $sut = new Pagination([], '', 5, 10, [10], [], []);

        $actualResult = $sut->getExtendedNodes();

        $this->assertCount(2, $actualResult);
        $this->assertInstanceOf(Numbers::class, $actualResult[0]);
        $this->assertInstanceOf(Select::class, $actualResult[1]);
    }

    public function testTemplateIsChangeable()
    {
        $sut = new Pagination([], '', 5, 10, [10], [], []);

        $sut->setTemplate('<foo>%1$s</foo><bar>%2$s%3$s</bar>');

        $this->assertMatchesRegularExpression('/\<foo\>.*\<\/foo\>\<bar\>.*\<\/bar\>/', (string)$sut);
    }

    public function testSetSortedUrlCanSetsUrlOnNumbersBeforeTotalCountIsSet()
    {
        $url = '/foo?';

        $sut = new Pagination([], '', 5, 10, [10], [], []);

        $sut->setSortedUrl($url);

        $sut->setTotalCount(100);

        $actualResult = (string)$sut;

        $this->assertStringContainsString($url, $actualResult);
    }

    public function testSetSortedUrlCanNotSetsUrlOnNumbersAfterTotalCountIsSet()
    {
        $url = '/foo?';

        $sut = new Pagination([], '', 5, 10, [10], [], []);

        $sut->setTotalCount(100);

        $sut->setSortedUrl($url);

        $actualResult = (string)$sut;

        $this->assertStringNotContainsString($url, $actualResult);
    }
}
