<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Cell;

use AbterPhp\Framework\Html\Attributes;
use AbterPhp\Framework\Html\ComponentTest;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\Node;
use AbterPhp\Framework\TestDouble\Html\Component\StubAttributeFactory;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;

class CellTest extends ComponentTest
{
    protected const CONTENT = 'foo';
    protected const GROUP   = 'bar';

    /**
     * @return array[]
     */
    public function renderProvider(): array
    {
        $attribs = StubAttributeFactory::createAttributes();

        return [
            'simple'               => ['ABC', 'a', null, null, null, "<td>ABC</td>"],
            'with attributes'      => ['ABC', 'a', $attribs, null, null, "<td$attribs>ABC</td>"],
            'missing translations' => ['ABC', 'a', null, [], null, "<td>ABC</td>"],
            'custom tag'           => ['ABC', 'a', null, null, 'mytd', "<mytd>ABC</mytd>"],
            'with translations'    => ['ABC', 'a', null, ['ABC' => 'CBA'], null, "<td>CBA</td>"],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param INode[]|INode|string|null $content
     * @param string                    $group
     * @param Attributes|null           $attributes
     * @param array|null                $translations
     * @param string|null               $tag
     * @param string                    $expectedResult
     */
    public function testRender(
        $content,
        string $group,
        ?Attributes $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ): void {
        $sut = $this->createElement($content, $group, $attributes, $translations, $tag);

        $actualResult1 = (string)$sut;
        $actualResult2 = (string)$sut;

        $this->assertSame($expectedResult, $actualResult1);
        $this->assertSame($expectedResult, $actualResult2);
    }

    public function testGetNodesDefault(): void
    {
        $defaultNode = new Node(static::CONTENT);

        $expectedNodes = [$defaultNode];

        $sut = $this->createNode();

        $actualResult = $sut->getNodes();

        $this->assertEquals($expectedNodes, $actualResult);
    }

    public function testGetNodes(): void
    {
        $defaultNode = new Node(static::CONTENT);

        $node1 = new Node('1');
        $node2 = new Node(new Node('2'));

        $expectedNodes = [$defaultNode, $node2, $node1, $node1, $node1];

        $sut = $this->createNode();

        $sut[]  = $node1;
        $sut[]  = $node1;
        $sut[1] = $node2;
        $sut[2] = $node1;
        $sut[3] = $node1;
        $sut[]  = $node1;

        $actualResult = $sut->getNodes();

        $this->assertEquals($expectedNodes, $actualResult);
    }

    public function testGetExtendedNodes(): void
    {
        $defaultNode = new Node(static::CONTENT);

        $node1 = new Node('1');
        $node2 = new Node(new Node('2'));

        $expectedNodes = [$defaultNode, $node2, $node1, $node1, $node1];

        $sut = $this->createNode();

        $sut[]  = $node1;
        $sut[]  = $node1;
        $sut[1] = $node2;
        $sut[2] = $node1;
        $sut[3] = $node1;
        $sut[]  = $node1;

        $actualResult = $sut->getExtendedNodes();

        $this->assertEquals($expectedNodes, $actualResult);
    }

    public function testGetDescendantNodes(): void
    {
        $defaultNode = new Node(static::CONTENT);

        $node1 = new Node('1');
        $node2 = new Node(new Node('2'));

        $expectedNodes = [$defaultNode, $node2, $node1, $node1, $node1];

        $sut = $this->createNode();

        $sut[]  = $node1;
        $sut[]  = $node1;
        $sut[1] = $node2;
        $sut[2] = $node1;
        $sut[3] = $node1;
        $sut[]  = $node1;

        $actualResult = $sut->getDescendantNodes();

        $this->assertEquals($expectedNodes, $actualResult);
    }

    public function testGetExtendedDescendantNodes(): void
    {
        $defaultNode = new Node(static::CONTENT);

        $node1 = new Node('1');
        $node2 = new Node(new Node('2'));

        $expectedNodes = [$defaultNode, $node2, $node1, $node1, $node1];

        $sut = $this->createNode();

        $sut[]  = $node1;
        $sut[]  = $node1;
        $sut[1] = $node2;
        $sut[2] = $node1;
        $sut[3] = $node1;
        $sut[]  = $node1;

        $actualResult = $sut->getExtendedDescendantNodes();

        $this->assertEquals($expectedNodes, $actualResult);
    }

    /**
     * @return array
     */
    public function toStringReturnsRawContentByDefaultProvider(): array
    {
        return [
            'string' => ['foo', '<td>foo</td>'],
        ];
    }

    /**
     * @dataProvider toStringReturnsRawContentByDefaultProvider
     *
     * @param mixed  $rawContent
     * @param string $expectedResult
     */
    public function testToStringReturnsRawContentByDefault($rawContent, string $expectedResult): void
    {
        $sut = $this->createNode($rawContent);

        $this->assertStringContainsString($expectedResult, (string)$sut);
    }

    /**
     * @return array
     */
    public function toStringCanReturnTranslatedContentProvider(): array
    {
        $translations = ['foo' => 'bar'];

        return [
            'string' => ['foo', $translations, '<td>bar</td>'],
        ];
    }

    /**
     * @dataProvider toStringCanReturnTranslatedContentProvider
     *
     * @param mixed  $rawContent
     * @param array  $translations
     * @param string $expectedResult
     */
    public function testToStringCanReturnTranslatedContent(
        $rawContent,
        array $translations,
        string $expectedResult
    ): void {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $sut = $this->createNode($rawContent);

        $sut->setTranslator($translatorMock);

        $this->assertStringContainsString($expectedResult, (string)$sut);
    }

    /**
     * @dataProvider isMatchProvider
     *
     * @param string|null $className
     * @param string[]    $intents
     * @param bool        $expectedResult
     */
    public function testIsMatch(?string $className, array $intents, bool $expectedResult): void
    {
        $sut = $this->createNode();
        $sut->setIntent('foo', 'bar');

        $actualResult = $sut->isMatch($className, ...$intents);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testGroupRetrievesOriginallyProvidedGroup(): void
    {
        $sut = $this->createNode();

        $actualResult = $sut->getGroup();

        $this->assertSame(static::GROUP, $actualResult);
    }

    /**
     * @param INode[]|INode|string|null $content
     * @param string                    $group
     * @param Attributes|null           $attributes
     * @param array|null                $translations
     * @param string|null               $tag
     *
     * @return Cell
     */
    protected function createElement(
        $content,
        string $group,
        ?Attributes $attributes,
        ?array $translations,
        ?string $tag
    ): Cell {
        $cell = new Cell($content, $group, [], $attributes, $tag);

        $cell->setTranslator(MockTranslatorFactory::createSimpleTranslator($this, $translations));

        return $cell;
    }

    /**
     * @param mixed $content
     *
     * @return Cell
     */
    private function createNode($content = null): INode
    {
        $content = $content ?: static::CONTENT;

        return $this->createElement($content, static::GROUP, null, null, null);
    }
}
