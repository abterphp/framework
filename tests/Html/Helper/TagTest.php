<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Helper;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attribute;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase
{
    /**
     * @return array[]
     */
    public function toStringProvider(): array
    {
        $attribs = Attributes::fromArray(['foo' => 'bar', 'bar' => 'baz']);
        $str     = Attributes::toString($attribs);

        return [
            'empty'                                => [Html5::TAG_ARTICLE, '', [], '<article></article>'],
            'normal'                               => [Html5::TAG_ARTICLE, 'foo', [], '<article>foo</article>'],
            'single-tag'                           => [Html5::TAG_BR, '', [], '<br>'],
            'single-tag-with-content'              => [Html5::TAG_BR, 'foo', [], '<br>'],
            'empty-with-attribs'                   => [Html5::TAG_ARTICLE, '', $attribs, "<article$str></article>"],
            'normal-with-attribs'                  => [Html5::TAG_SPAN, 'foo', $attribs, "<span$str>foo</span>"],
            'single-tag-with-attribs'              => [Html5::TAG_BR, '', $attribs, "<br$str>"],
            'single-tag-with-content-with-attribs' => [Html5::TAG_BR, 'foo', $attribs, "<br$str>"],
        ];
    }

    /**
     * @dataProvider toStringProvider
     *
     * @param string                  $tag
     * @param string                  $content
     * @param array<string,Attribute> $attributes
     * @param string                  $expectedResult
     */
    public function testToString(string $tag, string $content, array $attributes, string $expectedResult): void
    {
        $actualResult = Tag::toString($tag, $content, $attributes);

        $this->assertEquals($expectedResult, $actualResult);
    }
}
