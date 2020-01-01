<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Helper;

use PHPUnit\Framework\TestCase;

class StringHelperTest extends TestCase
{
    /**
     * @return array
     */
    public function plainToHtmlProvider(): array
    {
        return [
            'empty'                        => ['', '', '', ''],
            'one line w/o pre- or postfix' => ['foo', '', '', 'foo'],
            'one line'                     => ['foo', '<strong>', '</strong>', '<strong>foo</strong>'],
            'four lines'                   => [
                "hi!\nhello!\nbye!\nbye bye!",
                '<strong>',
                '</strong>',
                "<strong>hi!</strong>\n<strong>hello!</strong>\n<strong>bye!</strong>\n<strong>bye bye!</strong>",
            ],
        ];
    }

    /**
     * @dataProvider plainToHtmlProvider
     *
     * @param string $plainText
     * @param string $prefix
     * @param string $postfix
     * @param string $expectedResult
     */
    public function testPlainToHtml(string $plainText, string $prefix, string $postfix, string $expectedResult)
    {
        $actualResult = StringHelper::plainToHtml($plainText, $prefix, $postfix);

        $this->assertSame($expectedResult, $actualResult);
    }
}
