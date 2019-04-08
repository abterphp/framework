<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Cell;

use AbterPhp\Framework\Html\Component\StubAttributeFactory;
use AbterPhp\Framework\Html\ComponentTest;
use AbterPhp\Framework\Html\Helper\ArrayHelper;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\I18n\MockTranslatorFactory;

class CellTest extends ComponentTest
{
    /**
     * @return array
     */
    public function renderProvider()
    {
        $attribs = StubAttributeFactory::createAttributes();
        $str     = ArrayHelper::toAttributes($attribs);

        return [
            'simple'               => ['ABC', 'a', [], null, null, "<td>ABC</td>"],
            'with attributes'      => ['ABC', 'a', $attribs, null, null, "<td$str>ABC</td>"],
            'missing translations' => ['ABC', 'a', [], [], null, "<td>ABC</td>"],
            'custom tag'           => ['ABC', 'a', [], null, 'mytd', "<mytd>ABC</mytd>"],
            'with translations'    => ['ABC', 'a', [], ['ABC' => 'CBA'], null, "<td>CBA</td>"],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param INode[]|INode|string|null $content
     * @param string                    $group
     * @param array                     $attributes
     * @param array|null                $translations
     * @param string|null               $tag
     * @param string                    $expectedResult
     */
    public function testRender(
        $content,
        string $group,
        array $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ) {
        $sut = $this->createElement($content, $group, $attributes, $translations, $tag);

        $actualResult1 = (string)$sut;
        $actualResult2 = (string)$sut;

        $this->assertSame($expectedResult, $actualResult1);
        $this->assertSame($expectedResult, $actualResult2);
    }

    /**
     * @param INode[]|INode|string|null $content
     * @param string                    $group
     * @param array                     $attributes
     * @param array|null                $translations
     * @param string|null               $tag
     *
     * @return Cell
     */
    protected function createElement(
        $content,
        string $group,
        array $attributes,
        ?array $translations,
        ?string $tag
    ): Cell {
        $cell = new Cell($content, $group, [], $attributes, $tag);

        $cell->setTranslator(MockTranslatorFactory::createSimpleTranslator($this, $translations));

        return $cell;
    }
}
