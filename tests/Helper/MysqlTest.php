<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Helper;

use PHPUnit\Framework\TestCase;

class MysqlTest extends TestCase
{
    public function nullableParamProvider(): array
    {
        return [
            'null'                     => [null, \PDO::PARAM_STR, [null, \PDO::PARAM_NULL]],
            'empty string'             => ['', \PDO::PARAM_STR, ['', \PDO::PARAM_STR]],
            'numeric string as string' => ['23.4', \PDO::PARAM_STR, ['23.4', \PDO::PARAM_STR]],
            'numeric string as int'    => ['23.4', \PDO::PARAM_INT, ['23.4', \PDO::PARAM_INT]],
            'number as string'         => [23.4, \PDO::PARAM_STR, [23.4, \PDO::PARAM_STR]],
            'number as int'            => [23.4, \PDO::PARAM_INT, [23.4, \PDO::PARAM_INT]],
            'false as bool'            => [false, \PDO::PARAM_BOOL, [false, \PDO::PARAM_BOOL]],
            'true as bool'             => [true, \PDO::PARAM_BOOL, [true, \PDO::PARAM_BOOL]],
            'datetime'                 => [
                new \DateTime('2010-11-27 09:08:59'),
                \PDO::PARAM_STR,
                ['2010-11-27 09:08:59', \PDO::PARAM_STR],
            ],
            'date'                     => [
                new \DateTime('2010-11-27 09:08:59'),
                \PDO::PARAM_STR,
                ['2010-11-27', \PDO::PARAM_STR],
                Mysql::OPTION_PREFER_DATE,
            ],
        ];
    }

    /**
     * @dataProvider nullableParamProvider
     *
     * @param       $value
     * @param int   $type
     * @param array $expectedResult
     * @param int   $option
     */
    public function testNullableParam($value, int $type, array $expectedResult, int $option = Mysql::OPTION_EMPTY)
    {
        $actualResult = Mysql::nullableParam($value, $type, $option);

        $this->assertSame($expectedResult, $actualResult);
    }
}
