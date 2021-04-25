<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Helper;

use PHPUnit\Framework\TestCase;

class CssTest extends TestCase
{
    /**
     * @return array[]
     */
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
        $actualResult = Css::toStyles($styles);

        $this->assertEquals($expectedResult, $actualResult);
    }
}
