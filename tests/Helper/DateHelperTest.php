<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Helper;

use PHPUnit\Framework\TestCase;

class DateHelperTest extends TestCase
{
    /**
     * @return array
     */
    public function mysqlDateProvider(): array
    {
        return [
            [new \DateTime('2010-11-27 09:08:59'), '2010-11-27'],
        ];
    }

    /**
     * @dataProvider mysqlDateProvider
     *
     * @param \DateTime|null $date
     * @param string         $expectedResult
     */
    public function testMysqlDate(?\DateTime $date, string $expectedResult)
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
            [new \DateTime('2010-11-27 09:08:59'), '2010-11-27 09:08:59'],
        ];
    }

    /**
     * @dataProvider mysqlDateTimeProvider
     *
     * @param \DateTime|null $dateTime
     * @param string         $expectedResult
     */
    public function testMysqlDateTime(?\DateTime $dateTime, string $expectedResult)
    {
        $actualResult = DateHelper::mysqlDateTime($dateTime);

        $this->assertSame($expectedResult, $actualResult);
    }
}
