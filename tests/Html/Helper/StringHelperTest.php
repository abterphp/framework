<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Helper;

use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase
{
    public function wrapInTagDataProvider(): array
    {
        return [
            'empty'           => ['', null, [], ''],
            'simple'          => ['A', null, [], 'A'],
            'html'            => ['A', 'B', [], '<B>A</B>'],
            'with-attributes' => [
                'A',
                'B',
                ['foo' => ['foo'], 'bar' => ['baz']],
                "<B foo=\"foo\" bar=\"baz\">A</B>",
            ],
        ];
    }

    /**
     * @dataProvider wrapInTagDataProvider
     *
     * @param string      $content
     * @param string|null $tag
     * @param string[][]  $attributes
     * @param string      $expectedResult
     */
    public function testWrapInTag(
        string $content,
        ?string $tag,
        array $attributes,
        string $expectedResult
    ) {
        $actualResult = StringHelper::wrapInTag($content, $tag, $attributes);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function wrapByLinesProvider(): array
    {
        return [
            'empty'    => ['', '', ''],
            'one line' => ['foo', 'strong', '<strong>foo</strong>'],
            'complex'  => [
                "hi!\nhello!\nbye!\nbye bye!",
                'p',
                "<p>hi!</p>\n<p>hello!</p>\n<p>bye!</p>\n<p>bye bye!</p>",
            ],
        ];
    }

    /**
     * @dataProvider wrapByLinesProvider
     *
     * @param string $text
     * @param string $tag
     * @param string $expectedResult
     */
    public function testWrapByLines(string $text, string $tag, string $expectedResult)
    {
        $actualResult = StringHelper::wrapByLines($text, $tag);

        $this->assertSame($expectedResult, $actualResult);
    }
}
