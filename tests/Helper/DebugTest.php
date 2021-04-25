<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Helper;

use PHPUnit\Framework\TestCase;

class DebugTest extends TestCase
{
    /**
     * @return array[]
     */
    public function mysqlDateProvider(): array
    {
        return [
            ['foo %d bar %s', 15, ['baz'], 'foo 15 bar baz'],
        ];
    }

    /**
     * @dataProvider mysqlDateProvider
     *
     * @param string $str
     * @param        $val
     * @param array  $extra
     * @param string $expectedResult
     */
    public function testMysqlDate(string $str, $val, array $extra, string $expectedResult): void
    {
        $actualResult = Debug::prettyPrint($str, $val, ...$extra);

        $this->assertSame($expectedResult, $actualResult);
    }
}
