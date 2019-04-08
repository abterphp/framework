<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Navigation;

use AbterPhp\Framework\Html\ComponentTest;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\Node;

class ItemTest extends ComponentTest
{
    public function testDefaultToString()
    {
        $sut = $this->createNode();

        $this->assertSame('<li></li>', (string)$sut);
    }

    /**
     * @return array
     */
    public function toStringWithTranslationProvider(): array
    {
        return [
            ['AAA', ['AAA' => 'BBB'], '<li>BBB</li>'],
        ];
    }

    /**
     * @return array
     */
    public function toStringReturnsRawContentByDefaultProvider(): array
    {
        return [
            'string'  => ['foo', '<li>foo</li>'],
            'INode'   => [new Node('foo'), '<li>foo</li>'],
            'INode[]' => [[new Node('foo')], '<li>foo</li>'],
        ];
    }

    /**
     * @return array
     */
    public function toStringCanReturnTranslatedContentProvider(): array
    {
        $translations = ['foo' => 'bar'];

        return [
            'string'  => ['foo', $translations, '<li>bar</li>'],
            'INode'   => [new Node('foo'), $translations, '<li>bar</li>'],
            'INode[]' => [[new Node('foo')], $translations, '<li>bar</li>'],
        ];
    }

    /**
     * @param INode[]|INode|string|null $content
     *
     * @return Item
     */
    protected function createNode($content = null): INode
    {
        return new Item($content);
    }
}
