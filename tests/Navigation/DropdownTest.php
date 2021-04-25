<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Navigation;

use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\ITag;
use AbterPhp\Framework\Html\Node;
use AbterPhp\Framework\Html\Tag;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class DropdownTest extends TestCase
{
    public function testDefaultToString(): void
    {
        $sut = $this->createDropdown();

        $this->assertSame('<div><ul></ul></div>', (string)$sut);
    }

    public function testToStringWithoutWrapper(): void
    {
        $sut = $this->createDropdown();

        $sut->setWrapper(null);

        $this->assertSame('<ul></ul>', (string)$sut);
    }

    /**
     * @return array
     */
    public function toStringWithTranslationProvider(): array
    {
        return [
            ['AAA', ['AAA' => 'BBB'], '<div><ul><li>BBB</li></ul></div>'],
        ];
    }

    /**
     * @return array
     */
    public function toStringReturnsRawContentByDefaultProvider(): array
    {
        return [
            'IItem'   => [new Item('foo'), '<li>foo</li>'],
            'IItem[]' => [[new Item('foo')], '<li>foo</li>'],
        ];
    }

    /**
     * @return array
     */
    public function toStringCanReturnTranslatedContentProvider(): array
    {
        $translations = ['foo' => 'bar'];

        return [
            'IItem'   => [new Item('foo'), $translations, '<li>bar</li>'],
            'IItem[]' => [[new Item('foo')], $translations, '<li>bar</li>'],
        ];
    }

    /**
     * @return array
     */
    public function findProvider(): array
    {
        $node1 = new Item('1');
        $node2 = new Item('2');

        return [
            [[], $node1, null],
            [[$node2], $node1, null],
            [[$node1, $node2], $node1, 0],
            [[$node1, $node2], $node2, 1],
        ];
    }

    /**
     * @return array
     */
    public function findFirstChildProvider(): array
    {
        $item0   = new Item('0');
        $item1   = (new Item('1'))->setIntent('foo');
        $item2   = (new Item('2'))->setIntent('bar');
        $item3   = (new Item('3'))->setIntent('foo', 'bar');
        $content = [$item0, $item1, $item2, $item3];

        return [
            'INode-no-intent'               => [$content, INode::class, [], $item0],
            'INode-foo-intent'              => [$content, INode::class, ['foo'], $item1],
            'INode-bar-intent'              => [$content, INode::class, ['bar'], $item2],
            'INode-foo-and-bar-intent'      => [$content, INode::class, ['foo', 'bar'], $item3],
            'IComponent-foo-intent'         => [$content, ITag::class, ['foo'], $item1],
            'Component-foo-intent'          => [$content, Tag::class, ['foo'], $item1],
            'fail-INode-baz-intent'         => [$content, INode::class, ['baz'], null],
            'fail-INode-foo-and-baz-intent' => [$content, INode::class, ['foo', 'baz'], null],
            'fail-Node-foo-intent'          => [$content, Node::class, ['foo'], null],
            'Item-foo-intent'               => [$content, Item::class, ['foo'], $item1],
        ];
    }

    public function testGetPrefixGetsEmptyCollectionByDefault(): void
    {
        $sut = $this->createDropdown();

        $actualResult = $sut->getPrefix();

        $this->assertInstanceOf(INode::class, $actualResult);
    }

    public function testGetPrefixGetsLastPrefixSet(): void
    {
        $sut = $this->createDropdown();

        /** @var ITag|MockObject $collectionStub */
        $collectionStub = $this->createMock(INode::class);

        $sut->setPrefix($collectionStub);

        $actualResult = $sut->getPrefix();

        $this->assertSame($collectionStub, $actualResult);
    }

    public function testGetPostfixGetsEmptyCollectionByDefault(): void
    {
        $sut = $this->createDropdown();

        $actualResult = $sut->getPostfix();

        $this->assertInstanceOf(INode::class, $actualResult);
    }

    public function testGetPostfixGetsLastPrefixSet(): void
    {
        $sut = $this->createDropdown();

        /** @var ITag|MockObject $collectionStub */
        $collectionStub = $this->createMock(INode::class);

        $sut->setPostfix($collectionStub);

        $actualResult = $sut->getPostfix();

        $this->assertSame($collectionStub, $actualResult);
    }

    public function testGetWrapperCanReturnNull(): void
    {
        $sut = $this->createDropdown();

        $sut->setWrapper(null);

        $actualResult = $sut->getWrapper();

        $this->assertNull($actualResult);
    }

    public function testGetWrapperReturnsComponentByDefault(): void
    {
        $sut = $this->createDropdown();

        $actualResult = $sut->getWrapper();

        $this->assertInstanceOf(ITag::class, $actualResult);
    }

    public function testGetWrapperReturnsLastSetWrapper(): void
    {
        $sut = $this->createDropdown();

        /** @var ITag $componentStub */
        $componentStub = $this->createMock(ITag::class);

        $sut->setWrapper($componentStub);

        $actualResult = $sut->getWrapper();

        $this->assertSame($componentStub, $actualResult);
    }

    public function testGetExtendedNodesWithoutWrapper(): void
    {
        $sut = $this->createDropdown();
        $sut->setWrapper(null);

        $nodes = $sut->getExtendedNodes();

        $this->assertCount(2, $nodes);
    }

    public function testGetExtendedNodesWithWrapper(): void
    {
        $sut = $this->createDropdown();

        $nodes = $sut->getExtendedNodes();

        $this->assertCount(3, $nodes);
    }

    /**
     * @param INode[]|INode|string|null $content
     *
     * @return Dropdown
     */
    protected function createDropdown($content = null): Dropdown
    {
        return new Dropdown($content);
    }
}
