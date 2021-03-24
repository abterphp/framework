<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Helper;

use DateTime;
use PHPUnit\Framework\TestCase;

class DateHelperTest extends TestCase
{
    public function mysqlDateProvider(): array
    {
        return [
            [null, date("Y-m-d")],
            [new DateTime('2010-11-27 09:08:59'), '2010-11-27'],
        ];
    }

    /**
     * @dataProvider mysqlDateProvider
     *
     * @param DateTime|null $date
     * @param string        $expectedResult
     */
    public function testMysqlDate(?DateTime $date, string $expectedResult): void
    {
        $actualResult = DateHelper::mysqlDate($date);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function mysqlDateTimeProvider(): array
    {
        return [
            [null, time()],
            [new DateTime('2010-11-27 09:08:59'), mktime(9, 8, 59, 11, 27, 2010)],
        ];
    }

    /**
     * @dataProvider mysqlDateTimeProvider
     *
     * @param DateTime|null $dateTime
     * @param int           $expectedResult
     */
    public function testMysqlDateTime(?DateTime $dateTime, int $expectedResult): void
    {
        $actualResult = DateHelper::mysqlDateTime($dateTime);

        $actualTime = strtotime($actualResult);

        $this->assertEqualsWithDelta($expectedResult, $actualTime, 30.0);
    }
}
