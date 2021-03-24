<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Navigation;

use AbterPhp\Framework\Html\Collection;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\ComponentTest;
use AbterPhp\Framework\Html\ICollection;
use AbterPhp\Framework\Html\IComponent;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\Html\Node;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;

class DropdownTest extends ComponentTest
{
    public function testDefaultToString(): void
    {
        $sut = $this->createNode();

        $this->assertSame('<div><ul></ul></div>', (string)$sut);
    }

    public function testToStringWithoutWrapper(): void
    {
        $sut = $this->createNode();

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
            'IComponent-foo-intent'         => [$content, IComponent::class, ['foo'], $item1],
            'Component-foo-intent'          => [$content, Component::class, ['foo'], $item1],
            'fail-INode-baz-intent'         => [$content, INode::class, ['baz'], null],
            'fail-INode-foo-and-baz-intent' => [$content, INode::class, ['foo', 'baz'], null],
            'fail-Node-foo-intent'          => [$content, Node::class, ['foo'], null],
            'Item-foo-intent'               => [$content, Item::class, ['foo'], $item1],
        ];
    }

    /**
     * @return array
     */
    public function collectProvider(): array
    {
        $node0       = new Node('0');
        $item0       = new Item($node0);
        $node1       = new Node('1', ['foo']);
        $node2       = new Node('2', ['bar']);
        $node3       = new Node('3', ['foo', 'bar']);
        $collection4 = new Collection([$node3, $node1, $node2]);
        $item5       = new Item([$collection4]);
        $content     = [$item0, $item5];

        $level0Expected     = [$item0, $item5];
        $level1Expected     = [$item0, $node0, $item5, $collection4];
        $defaultExpected    = [$item0, $node0, $item5, $collection4, $node3, $node1, $node2];
        $fooOnlyExpected    = [$node3, $node1];
        $fooBarOnlyExpected = [$node3];

        return [
            '0-depth'       => [$content, null, 0, [], $level0Expected],
            '1-depth'       => [$content, null, 1, [], $level1Expected],
            'default'       => [$content, null, -1, [], $defaultExpected],
            'inode-only'    => [$content, INode::class, -1, [], $defaultExpected],
            'stdclass-only' => [$content, \stdClass::class, -1, [], []],
            'foo-only'      => [$content, null, -1, ['foo'], $fooOnlyExpected],
            'foo-bar-only'  => [$content, null, -1, ['foo', 'bar'], $fooBarOnlyExpected],
        ];
    }

    public function testCountWithExplicitOffset(): void
    {
        $expectedResult = 2;

        $item1 = new Item('1');
        $item2 = new Item('2');

        $sut = $this->createNode();

        $sut[0] = $item1;
        $sut[1] = $item2;

        $this->assertSame($expectedResult, count($sut));
    }

    public function testCountWithMixedOffset(): void
    {
        $item1 = new Item('1');
        $item2 = new Item('2');

        $expectedCount = 5;

        $sut = $this->createNode();

        $sut[]  = $item1;
        $sut[]  = $item1;
        $sut[1] = $item2;
        $sut[2] = $item1;
        $sut[3] = $item1;
        $sut[]  = $item1;

        $this->assertSame($expectedCount, count($sut));
    }

    public function testArrayAccessWithoutOffset(): void
    {
        $item1 = new Item('1');
        $item2 = new Item('2');

        $sut = $this->createNode();

        $sut[] = $item1;
        $sut[] = $item2;

        $this->assertSame($item1, $sut[0]);
        $this->assertSame($item2, $sut[1]);
    }

    public function testArrayAccessWithExplicitOffset(): void
    {
        $item1 = new Item('1');
        $item2 = new Item('2');

        $sut = $this->createNode();

        $sut[0] = $item1;
        $sut[1] = $item2;

        $this->assertSame($item1, $sut[0]);
        $this->assertSame($item2, $sut[1]);
    }

    public function testArrayAccessThrowExceptionWhenMadeDirty(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $item1 = new Item('1');

        $sut = $this->createNode();

        $sut[1] = $item1;
    }

    public function testArrayAccessWithMixedOffset(): void
    {
        $item1 = new Item('1');
        $item2 = new Item('2');

        $sut = $this->createNode();

        $sut[]  = $item1;
        $sut[]  = $item1;
        $sut[1] = $item2;
        $sut[2] = $item1;
        $sut[3] = $item1;
        $sut[]  = $item1;

        $expectedNodes = [0 => $item1, 1 => $item2, 2 => $item1, 3 => $item1, 4 => $item1];
        foreach ($expectedNodes as $idx => $item) {
            $this->assertEquals($item, $sut[$idx]);
        }
    }

    /**
     * @return array
     */
    public function contentFailureProvider(): array
    {
        return [
            'bool'                    => [true],
            'non-node object'         => [new \StdClass()],
            'string wrapped'          => [['']],
            'non-node object wrapped' => [[new \StdClass()]],
            'node double wrapped'     => [[[new Node()]]],
        ];
    }

    public function testCountWithoutOffset(): void
    {
        $expectedResult = 2;

        $item1 = new Item('1');
        $item2 = new Item('2');

        $sut = $this->createNode();

        $sut[] = $item1;
        $sut[] = $item2;

        $this->assertSame($expectedResult, count($sut));
    }

    public function testSetContent(): void
    {
        $item1 = new Item('1');
        $item2 = new Item('2');

        $expectedNodes = [new Item('3')];

        $sut = $this->createNode();

        $sut[]  = $item1;
        $sut[]  = $item1;
        $sut[1] = $item2;
        $sut[2] = $item1;
        $sut[3] = $item1;
        $sut[]  = $item1;

        $sut->setContent('3');

        $actualResult = $sut->getNodes();

        $this->assertEquals($expectedNodes, $actualResult);
    }

    public function testGetNodes(): void
    {
        $item1 = new Item('1');
        $item2 = new Item('2');

        $expectedNodes = [$item1, $item2, $item1, $item1, $item1];

        $sut = $this->createNode();

        $sut[]  = $item1;
        $sut[]  = $item1;
        $sut[1] = $item2;
        $sut[2] = $item1;
        $sut[3] = $item1;
        $sut[]  = $item1;

        $actualResult = $sut->getNodes();

        $this->assertEquals($expectedNodes, $actualResult);
    }

    public function testGetExtendedNodes(): void
    {
        $item1 = new Item('1');
        $item2 = new Item('2');

        $expectedNodes = [$item1, $item2, $item1, $item1, $item1];

        $sut = $this->createNode();

        $sut[]  = $item1;
        $sut[]  = $item1;
        $sut[1] = $item2;
        $sut[2] = $item1;
        $sut[3] = $item1;
        $sut[]  = $item1;

        $actualResult = $sut->getExtendedNodes();

        $this->assertCount(8, $actualResult);
        $this->assertInstanceOf(Collection::class, $actualResult[0]);
        $this->assertInstanceOf(Collection::class, $actualResult[1]);
        $this->assertInstanceOf(Component::class, $actualResult[2]);
        $this->assertSame($expectedNodes, array_slice($actualResult, 3));
    }

    public function testGetDescendantNodes(): void
    {
        $itemContent1 = new Node('1');
        $itemContent2 = new Node('2');

        $item1 = new Item($itemContent1);
        $item2 = new Item($itemContent2);

        $expectedNodes = [
            $item1, $itemContent1,
            $item2, $itemContent2,
            $item1, $itemContent1,
            $item1, $itemContent1,
            $item1, $itemContent1,
        ];

        $sut = $this->createNode();

        $sut[]  = $item1;
        $sut[]  = $item1;
        $sut[1] = $item2;
        $sut[2] = $item1;
        $sut[3] = $item1;
        $sut[]  = $item1;

        $actualResult = $sut->getDescendantNodes();

        $this->assertEquals($expectedNodes, $actualResult);
    }

    public function testGetExtendedDescendantNodes(): void
    {
        $itemContent1 = new Node('1');
        $itemContent2 = new Node('2');

        $item1 = new Item($itemContent1);
        $item2 = new Item($itemContent2);

        $expectedNodes = [
            $item1, $itemContent1,
            $item2, $itemContent2,
            $item1, $itemContent1,
            $item1, $itemContent1,
            $item1, $itemContent1,
        ];

        $sut = $this->createNode();

        $sut[]  = $item1;
        $sut[]  = $item1;
        $sut[1] = $item2;
        $sut[2] = $item1;
        $sut[3] = $item1;
        $sut[]  = $item1;

        $actualResult = $sut->getExtendedDescendantNodes();

        $this->assertCount(13, $actualResult);
        $this->assertInstanceOf(Collection::class, $actualResult[0]);
        $this->assertInstanceOf(Collection::class, $actualResult[1]);
        $this->assertInstanceOf(Component::class, $actualResult[2]);
        $this->assertSame($expectedNodes, array_slice($actualResult, 3));
    }

    public function testIterator(): void
    {
        $item1 = new Item('1');
        $item2 = new Item('2');

        $expectedKeys  = [0, 1, 2, 3, 4];
        $expectedNodes = [$item1, $item2, $item1, $item1, $item1];

        $sut = $this->createNode();

        $sut[]  = $item1;
        $sut[]  = $item1;
        $sut[1] = $item2;
        $sut[2] = $item1;
        $sut[3] = $item1;
        $sut[]  = $item1;

        $pos = 0;
        foreach ($sut as $key => $node) {
            $this->assertSame($expectedKeys[$pos], $key);
            $this->assertSame($expectedNodes[$pos], $node);
            $pos++;
        }
    }

    /**
     * @return array
     */
    public function insertBeforeProvider(): array
    {
        $needle = new Item('1');
        $item2  = new Item('2');
        $item3  = new Item('3');

        return [
            'empty-content'                  => [
                [],
                $needle,
                [$item2],
                false,
                [],
            ],
            'only-non-matching-content'      => [
                [$item2, $item3],
                $needle,
                [$needle, $item3],
                false,
                [$item2, $item3],
            ],
            'only-matching-content'          => [
                [$needle],
                $needle,
                [$item2, $item3],
                true,
                [$item2, $item3, $needle],
            ],
            'non-first-matching-content'     => [
                [$item2, $needle],
                $needle,
                [$item3, $item3],
                true,
                [$item2, $item3, $item3, $needle],
            ],
        ];
    }

    /**
     * @return array
     */
    public function insertAfterProvider(): array
    {
        $needle = new Item('1');
        $item2  = new Item('2');
        $item3  = new Item('3');

        return [
            'empty-content'                  => [
                [],
                $needle,
                [$item2],
                false,
                [],
            ],
            'only-non-matching-content'      => [
                [$item2, $item3],
                $needle,
                [$needle, $item3],
                false,
                [$item2, $item3],
            ],
            'only-matching-content'          => [
                [$needle],
                $needle,
                [$item2, $item3],
                true,
                [$needle, $item2, $item3],
            ],
            'non-last-matching-content'      => [
                [$needle, $item2],
                $needle,
                [$item3, $item3],
                true,
                [$needle, $item3, $item3, $item2],
            ],
        ];
    }

    /**
     * @return array
     */
    public function replaceProvider(): array
    {
        $needle = new Item('1');
        $item2  = new Item('2');
        $item3  = new Item('3');

        return [
            'empty-content'                  => [
                [],
                $needle,
                [$item2],
                false,
                [],
            ],
            'only-non-matching-content'      => [
                [$item2, $item3],
                $needle,
                [$needle, $item3],
                false,
                [$item2, $item3],
            ],
            'only-matching-content'          => [
                [$needle],
                $needle,
                [$item2, $item3],
                true,
                [$item2, $item3],
            ],
            'non-first-matching-content'     => [
                [$item2, $needle],
                $needle,
                [$item3, $item3],
                true,
                [$item2, $item3, $item3],
            ],
            'non-last-matching-content'      => [
                [$needle, $item2],
                $needle,
                [$item3, $item3],
                true,
                [$item3, $item3, $item2],
            ],
        ];
    }

    /**
     * @return array
     */
    public function removeProvider(): array
    {
        $needle = new Item('1');
        $item2  = new Item('2');
        $item3  = new Item('3');

        return [
            'empty-content'                  => [
                [],
                $needle,
                false,
                [],
            ],
            'only-non-matching-content'      => [
                [$item2, $item3],
                $needle,
                false,
                [$item2, $item3],
            ],
            'only-matching-content'          => [
                [$needle],
                $needle,
                true,
                [],
            ],
            'non-first-matching-content'     => [
                [$item2, $needle],
                $needle,
                true,
                [$item2],
            ],
            'non-last-matching-content'      => [
                [$needle, $item2],
                $needle,
                true,
                [$item2],
            ],
        ];
    }

    /**
     * @return array
     */
    public function isMatchProvider(): array
    {
        return [
            'INode-no-intent'               => [INode::class, [], true],
            'INode-foo-intent'              => [INode::class, ['foo'], true],
            'INode-bar-intent'              => [INode::class, ['bar'], true],
            'INode-foo-and-bar-intent'      => [INode::class, ['foo', 'bar'], true],
            'IComponent-foo-intent'         => [IComponent::class, ['foo'], true],
            'Component-foo-intent'          => [Component::class, ['foo'], true],
            'fail-INode-baz-intent'         => [INode::class, ['baz'], false],
            'fail-INode-foo-and-baz-intent' => [INode::class, ['foo', 'baz'], false],
            'fail-Node-foo-intent'          => [Node::class, ['foo'], false],
            'Dropdown-foo-intent'           => [Dropdown::class, ['foo'], true],
        ];
    }

    public function testArrayAccessUnset(): void
    {
        $node1 = new Item('1');

        $sut = $this->createNode();

        $sut[] = $node1;

        $this->assertTrue($sut->offsetExists(0));

        unset($sut[0]);

        $this->assertfalse($sut->offsetExists(0));
    }

    /**
     * @dataProvider toStringWithTranslationProvider
     *
     * @param        $content
     * @param array  $translations
     * @param string $expectedResult
     */
    public function testToStringWithTranslation($content, array $translations, string $expectedResult): void
    {
        $translator = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $sut = $this->createNode($content);
        $sut->setTranslator($translator);

        $this->assertSame($expectedResult, (string)$sut);
    }

    /**
     * @dataProvider isMatchProvider
     *
     * @param string|null $className
     * @param string[]    $intents
     * @param int|null    $expectedResult
     */
    public function testIsMatch(?string $className, array $intents, bool $expectedResult): void
    {
        $sut = $this->createNode();
        $sut->setIntent('foo', 'bar');

        $actualResult = $sut->isMatch($className, ...$intents);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testGetPrefixGetsEmptyCollectionByDefault(): void
    {
        $sut = $this->createNode();

        $actualResult = $sut->getPrefix();

        $this->assertInstanceOf(ICollection::class, $actualResult);
        $this->assertCount(0, $actualResult);
    }

    public function testGetPrefixGetsLastPrefixSet(): void
    {
        $sut = $this->createNode();

        /** @var ICollection $collectionStub */
        $collectionStub = $this->createMock(ICollection::class);

        $sut->setPrefix($collectionStub);

        $actualResult = $sut->getPrefix();

        $this->assertSame($collectionStub, $actualResult);
    }

    public function testGetPostfixGetsEmptyCollectionByDefault(): void
    {
        $sut = $this->createNode();

        $actualResult = $sut->getPostfix();

        $this->assertInstanceOf(ICollection::class, $actualResult);
        $this->assertCount(0, $actualResult);
    }

    public function testGetPostfixGetsLastPrefixSet(): void
    {
        $sut = $this->createNode();

        /** @var ICollection $collectionStub */
        $collectionStub = $this->createMock(ICollection::class);

        $sut->setPostfix($collectionStub);

        $actualResult = $sut->getPostfix();

        $this->assertSame($collectionStub, $actualResult);
    }

    public function testGetWrapperCanReturnNull(): void
    {
        $sut = $this->createNode();

        $sut->setWrapper(null);

        $actualResult = $sut->getWrapper();

        $this->assertNull($actualResult);
    }

    public function testGetWrapperReturnsComponentByDefault(): void
    {
        $sut = $this->createNode();

        $actualResult = $sut->getWrapper();

        $this->assertInstanceOf(IComponent::class, $actualResult);
    }

    public function testGetWrapperReturnsLastSetWrapper(): void
    {
        $sut = $this->createNode();

        /** @var IComponent $componentStub */
        $componentStub = $this->createMock(IComponent::class);

        $sut->setWrapper($componentStub);

        $actualResult = $sut->getWrapper();

        $this->assertSame($componentStub, $actualResult);
    }

    /**
     * @param INode[]|INode|string|null $content
     *
     * @return Dropdown
     */
    protected function createNode($content = null): INode
    {
        return new Dropdown($content);
    }
}
