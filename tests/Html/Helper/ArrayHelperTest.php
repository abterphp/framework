<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Helper;

use PHPUnit\Framework\TestCase;

class ArrayHelperTest extends TestCase
{
    public function toStylesProvider(): array
    {
        return [
            'empty' => [[], ''],
            'one'   => [['color' => 'red'], 'color: red'],
            'three' => [
                ['color' => 'red', 'background' => 'none', 'font-weight' => 'bold'],
                'color: red; background: none; font-weight: bold',
            ],
        ];
    }

    /**
     * @dataProvider toStylesProvider
     *
     * @param array  $styles
     * @param string $expectedResult
     */
    public function testToStyles(array $styles, string $expectedResult): void
    {
        $actualResult = ArrayHelper::toStyles($styles);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function toQueryProvider(): array
    {
        return [
            'empty'   => [[], ''],
            'simple'  => [['key' => 'value'], '?key=value'],
            'default' => [['key1' => 'value1', 'key2' => 'value2'], '?key1=value1&key2=value2'],
            'complex' => [['key1' => 'value1ő', 'key2' => 'value2'], '?key1=value1%C5%91&key2=value2'],
        ];
    }

    /**
     * @dataProvider toQueryProvider
     *
     * @param array  $parts
     * @param string $expectedResult
     */
    public function testToQuery(array $parts, string $expectedResult): void
    {
        $actualResult = ArrayHelper::toQuery($parts);

        $this->assertEquals($expectedResult, $actualResult);
    }
}
