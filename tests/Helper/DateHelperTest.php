<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Helper;

use PHPUnit\Framework\TestCase;

class DateHelperTest extends TestCase
{
    /**
     * @return array
     */
    public function formatProvider(): array
    {
        return [
            [new \DateTime('2010-11-27 09:08:59'), 'Y-m-d', '2010-11-27'],
            [new \DateTime('2010-11-27 09:08:59'), 'd.m.Y', '27.11.2010'],
            [null, 'Y-m-d', ''],
        ];
    }

    /**
     * @dataProvider formatProvider
     *
     * @param \DateTime|null $dateTime
     * @param string         $dateFormat
     * @param string         $expectedResult
     */
    public function testFormat(?\DateTime $dateTime, string $dateFormat, string $expectedResult)
    {
        putenv(sprintf('ADMIN_DATE_FORMAT=%s', $dateFormat));

        $actualResult = DateHelper::format($dateTime);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function formatDateTimeProvider(): array
    {
        return [
            [new \DateTime('2010-11-27 09:08:59'), 'Y-m-d H:i:s', '2010-11-27 09:08:59'],
            [new \DateTime('2010-11-27 09:08:59'), 'd.m.Y H:i:s', '27.11.2010 09:08:59'],
            [null, 'Y-m-d H:i:s', ''],
        ];
    }

    /**
     * @dataProvider formatDateTimeProvider
     *
     * @param \DateTime|null $dateTime
     * @param string         $dateFormat
     * @param string         $expectedResult
     */
    public function testFormatDateTime(?\DateTime $dateTime, string $dateFormat, string $expectedResult)
    {
        putenv(sprintf('ADMIN_DATETIME_FORMAT=%s', $dateFormat));

        $actualResult = DateHelper::formatDateTime($dateTime);

        $this->assertSame($expectedResult, $actualResult);
    }

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
     * @param string     $expectedResult
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
