<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use AbterPhp\Framework\I18n\MockTranslatorFactory;

class CollectionTest extends NodeTestCase
{
    /**
     * @return array
     */
    public function toStringReturnsRawContentByDefaultProvider(): array
    {
        return [
            'string'  => ['foo', 'foo'],
            'INode'   => [new Node('foo'), 'foo'],
            'INode[]' => [[new Node('foo')], 'foo'],
        ];
    }

    /**
     * @dataProvider toStringReturnsRawContentByDefaultProvider
     *
     * @param mixed  $rawContent
     * @param string $expectedResult
     */
    public function testToStringReturnsRawContentByDefault($rawContent, string $expectedResult)
    {
        $sut = $this->createNode($rawContent);

        $this->assertContains($expectedResult, (string)$sut);
    }

    /**
     * @return array
     */
    public function toStringCanReturnTranslatedContentProvider(): array
    {
        $translations = ['foo' => 'bar'];

        return [
            'string'  => ['foo', $translations, 'bar'],
            'INode'   => [new Node('foo'), $translations, 'bar'],
            'INode[]' => [[new Node('foo')], $translations, 'bar'],
        ];
    }

    /**
     * @dataProvider toStringCanReturnTranslatedContentProvider
     *
     * @param mixed  $rawContent
     * @param string $expectedResult
     */
    public function testToStringCanReturnTranslatedContent($rawContent, array $translations, string $expectedResult)
    {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $sut = $this->createNode($rawContent);

        $sut->setTranslator($translatorMock);

        $this->assertContains($expectedResult, (string)$sut);
    }

    /**
     * @dataProvider isMatchProvider
     *
     * @param string|null $className
     * @param string[]    $intents
     * @param int|null    $expectedResult
     */
    public function testIsMatch(?string $className, array $intents, bool $expectedResult)
    {
        $sut = $this->createNode();
        $sut->setIntent('foo', 'bar');

        $actualResult = $sut->isMatch($className, ...$intents);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testCountWithoutOffset()
    {
        $expectedResult = 2;

        $node1 = new Node('1');
        $node2 = new Node('2');

        $sut = $this->createNode();

        $sut[] = $node1;
        $sut[] = $node2;

        $this->assertSame($expectedResult, count($sut));
    }

    public function testCountWithExplicitOffset()
    {
        $expectedResult = 2;

        $node1 = new Node('1');
        $node2 = new Node('2');

        $sut = $this->createNode();

        $sut[0] = $node1;
        $sut[1] = $node2;

        $this->assertSame($expectedResult, count($sut));
    }

    public function testCountWithMixedOffset()
    {
        $node1 = new Node('1');
        $node2 = new Node('2');

        $expectedCount = 5;

        $sut = $this->createNode();

        $sut[]  = $node1;
        $sut[]  = $node1;
        $sut[1] = $node2;
        $sut[2] = $node1;
        $sut[3] = $node1;
        $sut[]  = $node1;

        $this->assertSame($expectedCount, count($sut));
    }

    public function testArrayAccessWithoutOffset()
    {
        $node1 = new Node('1');
        $node2 = new Node('2');

        $sut = $this->createNode();

        $sut[] = $node1;
        $sut[] = $node2;

        $this->assertSame($node1, $sut[0]);
        $this->assertSame($node2, $sut[1]);
    }

    public function testArrayAccessWithExplicitOffset()
    {
        $node1 = new Node('1');
        $node2 = new Node('2');

        $sut = $this->createNode();

        $sut[0] = $node1;
        $sut[1] = $node2;

        $this->assertSame($node1, $sut[0]);
        $this->assertSame($node2, $sut[1]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testArrayAccessThrowExceptionWhenMadeDirty()
    {
        $node1 = new Node('1');

        $sut = $this->createNode();

        $sut[1] = $node1;
    }

    public function testArrayAccessWithMixedOffset()
    {
        $node1 = new Node('1');
        $node2 = new Node('2');

        $expectedNodes = [0 => $node1, 1 => $node2, 2 => $node1, 3 => $node1, 4 => $node1];

        $sut = $this->createNode();

        $sut[]  = $node1;
        $sut[]  = $node1;
        $sut[1] = $node2;
        $sut[2] = $node1;
        $sut[3] = $node1;
        $sut[]  = $node1;

        $this->assertEquals($expectedNodes, $sut->getExtendedNodes());
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

    /**
     * @dataProvider contentFailureProvider
     *
     * @expectedException \InvalidArgumentException
     */
    public function testConstructFailure($item)
    {
        $this->createNode($item);
    }

    /**
     * @dataProvider contentFailureProvider
     *
     * @expectedException \InvalidArgumentException
     */
    public function testSetContentFailure($item)
    {
        $sut = $this->createNode();

        $sut->setContent($item);
    }

    /**
     * @return array
     */
    public function offsetSetFailureProvider(): array
    {
        $contentFailure = $this->contentFailureProvider();

        $offsetFailure = [
            'string'       => ['foo'],
            'node wrapped' => [[new Node()]],
        ];

        return array_merge($contentFailure, $offsetFailure);
    }

    /**
     * @dataProvider offsetSetFailureProvider
     *
     * @expectedException \InvalidArgumentException
     */
    public function testArrayAccessFailureWithoutOffset($item)
    {
        $sut = $this->createNode();

        $sut[] = $item;
    }

    /**
     * @dataProvider offsetSetFailureProvider
     *
     * @expectedException \InvalidArgumentException
     */
    public function testArrayAccessFailureWithExplicitOffset($item)
    {
        $sut = $this->createNode();

        $sut[] = $item;
    }

    public function testArrayAccessUnset()
    {
        $node1 = new Node('1');

        $sut = $this->createNode();

        $sut[] = $node1;

        $this->assertTrue($sut->offsetExists(0));

        unset($sut[0]);

        $this->assertfalse($sut->offsetExists(0));
    }

    public function testSetContent()
    {
        $node1 = new Node('1');
        $node2 = new Node('2');

        $expectedNodes = [new Node('3')];

        $sut = $this->createNode();

        $sut[]  = $node1;
        $sut[]  = $node1;
        $sut[1] = $node2;
        $sut[2] = $node1;
        $sut[3] = $node1;
        $sut[]  = $node1;

        $sut->setContent('3');

        $actualResult = $sut->getNodes();

        $this->assertEquals($expectedNodes, $actualResult);
    }

    public function testGetNodes()
    {
        $node1 = new Node('1');
        $node2 = new Node(new Node('2'));

        $expectedNodes = [$node1, $node2, $node1, $node1, $node1];

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

    public function testGetExtendedNodes()
    {
        $node1 = new Node('1');
        $node2 = new Node(new Node('2'));

        $expectedNodes = [$node1, $node2, $node1, $node1, $node1];

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

    public function testGetDescendantNodes()
    {
        $node1 = new Node('1');
        $node2 = new Node(new Node('2'));

        $expectedNodes = [$node1, $node2, $node1, $node1, $node1];

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

    public function testGetExtendedDescendantNodes()
    {
        $node1 = new Node('1');
        $node2 = new Node(new Node('2'));

        $expectedNodes = [$node1, $node2, $node1, $node1, $node1];

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

    public function testIterator()
    {
        $node1 = new Node('1');
        $node2 = new Node('2');

        $expectedKeys  = [0, 1, 2, 3, 4];
        $expectedNodes = [$node1, $node2, $node1, $node1, $node1];

        $sut = $this->createNode();

        $sut[]  = $node1;
        $sut[]  = $node1;
        $sut[1] = $node2;
        $sut[2] = $node1;
        $sut[3] = $node1;
        $sut[]  = $node1;

        $pos = 0;
        foreach ($sut as $key => $node) {
            $this->assertSame($expectedKeys[$pos], $key);
            $this->assertSame($expectedNodes[$pos], $node);
            $pos++;
        }
    }

    public function testGetRawContentReturnsNonTranslatedContent()
    {
        $this->assertTrue(true, 'No need to test getRawContent');
    }

    /**
     * @return array
     */
    public function insertBeforeProvider(): array
    {
        $needle = new Node('1');
        $node2  = new Node('2');
        $node3  = new Node('3');

        return [
            'empty-content'                  => [
                [],
                $needle,
                [$node2],
                false,
                [],
            ],
            'only-non-matching-content'      => [
                [$node2, $node3],
                $needle,
                [$needle, $node3],
                false,
                [$node2, $node3],
            ],
            'only-non-matching-content-deep' => [
                [$node2, $node3, new Collection([$node2, $node3])],
                $needle,
                [$needle, $node3],
                false,
                [$node2, $node3, new Collection([$node2, $node3])],
            ],
            'only-matching-content'          => [
                [$needle],
                $needle,
                [$node2, $node3],
                true,
                [$node2, $node3, $needle],
            ],
            'non-first-matching-content'     => [
                [$node2, $needle],
                $needle,
                [$node3, $node3],
                true,
                [$node2, $node3, $node3, $needle],
            ],
            'deep-first-matching-content'    => [
                [$node2, new Collection([$node2, $needle])],
                $needle,
                [$node3, $node3],
                true,
                [$node2, new Collection([$node2, $node3, $node3, $needle])],
            ],
        ];
    }

    /**
     * @dataProvider insertBeforeProvider
     *
     * @param INode[] $content
     * @param INode   $nodeToFind
     * @param INode[] $nodesToInsert
     * @param bool    $expectedResult
     * @param INode[] $expectedNodes
     */
    public function testInsertBefore(
        array $content,
        INode $nodeToFind,
        array $nodesToInsert,
        bool $expectedResult,
        array $expectedNodes
    ) {
        $sut = $this->createNode($content);

        $actualResult = $sut->insertBefore($nodeToFind, ...$nodesToInsert);

        $this->assertSame($expectedResult, $actualResult);
        $this->assertEquals($expectedNodes, $sut->getNodes());
    }

    /**
     * @return array
     */
    public function insertAfterProvider(): array
    {
        $needle = new Node('1');
        $node2  = new Node('2');
        $node3  = new Node('3');

        return [
            'empty-content'                  => [
                [],
                $needle,
                [$node2],
                false,
                [],
            ],
            'only-non-matching-content'      => [
                [$node2, $node3],
                $needle,
                [$needle, $node3],
                false,
                [$node2, $node3],
            ],
            'only-non-matching-content-deep' => [
                [$node2, $node3, new Collection([$node2, $node3])],
                $needle,
                [$needle, $node3],
                false,
                [$node2, $node3, new Collection([$node2, $node3])],
            ],
            'only-matching-content'          => [
                [$needle],
                $needle,
                [$node2, $node3],
                true,
                [$needle, $node2, $node3],
            ],
            'non-last-matching-content'      => [
                [$needle, $node2],
                $needle,
                [$node3, $node3],
                true,
                [$needle, $node3, $node3, $node2],
            ],
            'deep-first-matching-content'    => [
                [$node2, new Collection([$node2, $needle])],
                $needle,
                [$node3, $node3],
                true,
                [$node2, new Collection([$node2, $needle, $node3, $node3])],
            ],
        ];
    }

    /**
     * @dataProvider insertAfterProvider
     *
     * @param INode[] $content
     * @param INode   $nodeToFind
     * @param INode[] $nodesToInsert
     * @param bool    $expectedResult
     * @param INode[] $expectedNodes
     */
    public function testInsertAfter(
        array $content,
        INode $nodeToFind,
        array $nodesToInsert,
        bool $expectedResult,
        array $expectedNodes
    ) {
        $sut = $this->createNode($content);

        $actualResult = $sut->insertAfter($nodeToFind, ...$nodesToInsert);

        $this->assertSame($expectedResult, $actualResult);
        $this->assertEquals($expectedNodes, $sut->getNodes());
    }

    /**
     * @return array
     */
    public function replaceProvider(): array
    {
        $needle = new Node('1');
        $node2  = new Node('2');
        $node3  = new Node('3');

        return [
            'empty-content'                  => [
                [],
                $needle,
                [$node2],
                false,
                [],
            ],
            'only-non-matching-content'      => [
                [$node2, $node3],
                $needle,
                [$needle, $node3],
                false,
                [$node2, $node3],
            ],
            'only-non-matching-content-deep' => [
                [$node2, $node3, new Collection([$node2, $node3])],
                $needle,
                [$needle, $node3],
                false,
                [$node2, $node3, new Collection([$node2, $node3])],
            ],
            'only-matching-content'          => [
                [$needle],
                $needle,
                [$node2, $node3],
                true,
                [$node2, $node3],
            ],
            'non-first-matching-content'     => [
                [$node2, $needle],
                $needle,
                [$node3, $node3],
                true,
                [$node2, $node3, $node3],
            ],
            'non-last-matching-content'      => [
                [$needle, $node2],
                $needle,
                [$node3, $node3],
                true,
                [$node3, $node3, $node2],
            ],
            'deep-first-matching-content'    => [
                [$node2, new Collection([$node2, $needle])],
                $needle,
                [$node3, $node3],
                true,
                [$node2, new Collection([$node2, $node3, $node3])],
            ],
        ];
    }

    /**
     * @dataProvider replaceProvider
     *
     * @param INode[] $content
     * @param INode   $nodeToFind
     * @param INode[] $nodesToInsert
     * @param bool    $expectedResult
     * @param INode[] $expectedNodes
     */
    public function testReplace(
        array $content,
        INode $nodeToFind,
        array $nodesToInsert,
        bool $expectedResult,
        array $expectedNodes
    ) {
        $sut = $this->createNode($content);

        $actualResult = $sut->replace($nodeToFind, ...$nodesToInsert);

        $this->assertSame($expectedResult, $actualResult);
        $this->assertEquals($expectedNodes, $sut->getNodes());
    }

    /**
     * @return array
     */
    public function removeProvider(): array
    {
        $needle = new Node('1');
        $node2  = new Node('2');
        $node3  = new Node('3');

        return [
            'empty-content'                  => [
                [],
                $needle,
                false,
                [],
            ],
            'only-non-matching-content'      => [
                [$node2, $node3],
                $needle,
                false,
                [$node2, $node3],
            ],
            'only-non-matching-content-deep' => [
                [$node2, $node3, new Collection([$node2, $node3])],
                $needle,
                false,
                [$node2, $node3, new Collection([$node2, $node3])],
            ],
            'only-matching-content'          => [
                [$needle],
                $needle,
                true,
                [],
            ],
            'non-first-matching-content'     => [
                [$node2, $needle],
                $needle,
                true,
                [$node2],
            ],
            'non-last-matching-content'      => [
                [$needle, $node2],
                $needle,
                true,
                [$node2],
            ],
            'deep-first-matching-content'    => [
                [$node2, new Collection([$node2, $needle])],
                $needle,
                true,
                [$node2, new Collection([$node2])],
            ],
        ];
    }

    /**
     * @dataProvider removeProvider
     *
     * @param INode[] $content
     * @param INode   $nodeToFind
     * @param bool    $expectedResult
     * @param INode[] $expectedNodes
     */
    public function testRemove(
        array $content,
        INode $nodeToFind,
        bool $expectedResult,
        array $expectedNodes
    ) {
        $sut = $this->createNode($content);

        $actualResult = $sut->remove($nodeToFind);

        $this->assertSame($expectedResult, $actualResult);
        $this->assertEquals($expectedNodes, $sut->getNodes());
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
            'fail-IComponent-foo-intent'    => [IComponent::class, ['foo'], false],
            'fail-Component-foo-intent'     => [Component::class, ['foo'], false],
            'fail-INode-baz-intent'         => [INode::class, ['baz'], false],
            'fail-INode-foo-and-baz-intent' => [INode::class, ['foo', 'baz'], false],
            'fail-Node-foo-intent'          => [Node::class, ['foo'], false],
        ];
    }

    /**
     * @return array
     */
    public function hasIntentProvider(): array
    {
        return [
            [[], 'foo', false],
            [['foo'], 'foo', true],
            [['bar'], 'foo', false],
            [['foo', 'bar', 'baz'], 'bar', true],
        ];
    }

    /**
     * @dataProvider hasIntentProvider
     *
     * @param array  $intents
     * @param string $intentToCheck
     * @param bool   $expectedResult
     */
    public function testHasIntentChecksIfAGivenIntentHasBeenSet(
        array $intents,
        string $intentToCheck,
        bool $expectedResult
    ) {
        $sut = $this->createNode();

        $sut->setIntent(...$intents);

        $actualResult = $sut->hasIntent($intentToCheck);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @param INode[]|INode|string|null $content
     *
     * @return Collection
     */
    private function createNode($content = null): INode
    {
        return new Collection($content);
    }
}
