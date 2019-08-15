<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Helper;

use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase
{
    /**
     * @return array
     */
    public function wrapInTagDataProvider()
    {
        return [
            'empty'           => ['', null, [], '', ''],
            'simple'          => ['A', null, [], '', 'A'],
            'html'            => ['A', 'B', [], '', '<B>A</B>'],
            'with-attributes' => [
                'A',
                'B',
                ['foo' => ['foo'], 'bar' => ['baz']],
                '',
                "<B foo=\"foo\" bar=\"baz\">A</B>",
            ],
            'whitespace'      => [
                'A',
                'B',
                ['foo' => ['foo'], 'bar' => ['baz']],
                '  ',
                "  <B foo=\"foo\" bar=\"baz\">\nA\n  </B>",
            ],
        ];
    }

    /**
     * @dataProvider wrapInTagDataProvider
     *
     * @param string      $content
     * @param string|null $tag
     * @param string[][]  $attributes
     * @param string      $whitespace
     * @param string      $expectedResult
     */
    public function testWrapInTag(
        string $content,
        ?string $tag,
        array $attributes,
        string $whitespace,
        string $expectedResult
    ) {
        $actualResult = StringHelper::wrapInTag($content, $tag, $attributes, $whitespace);

        $this->assertSame($expectedResult, $actualResult);
    }
}
