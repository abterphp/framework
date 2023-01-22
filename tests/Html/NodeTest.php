<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use AbterPhp\Framework\I18n\ITranslator;
use AbterPhp\Framework\TestDouble\Html\CollectionStub;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use Closure;
use PHPUnit\Framework\TestCase;

class NodeTest extends TestCase
{
    /** @var Node - System Under Test */
    protected Node $sut;

    public function setUp(): void
    {
        $this->sut = new Node();
    }

    /**
     * @return array[]
     */
    public function setContentProvider(): array
    {
        $fooNode     = new Node('foo');
        $fooBarNode  = new Node(['foo', 'bar']);
        $fooNodeNode = new Node($fooNode, 'baz');

        return [
            'null'            => [null, '', []],
            'false'           => [false, '', []],
            'true'            => [true, '1', []],
            '12.43'           => [12.43, '12.43', []],
            'string'          => ['foo', 'foo', []],
            'array'           => [['foo'], 'foo', []],
            'strings'         => [['foo', 'bar'], 'foobar', []],
            'node'            => [$fooNode, 'foo', [$fooNode]],
            'node-with-array' => [$fooBarNode, 'foobar', [$fooBarNode]],
            'node-node'       => [$fooNodeNode, 'foo', [$fooNodeNode]],
            'node-nodes'      => [[$fooNode, $fooNodeNode], 'foofoo', [$fooNode, $fooNodeNode]],
            'mixed'           => [[$fooNode, 'bar', $fooNodeNode], 'foobarfoo', [$fooNode, $fooNodeNode]],
        ];
    }

    /**
     * @dataProvider setContentProvider
     *
     * @param        $content
     * @param string $expectedAsString
     * @param array  $expectedNodes
     */
    public function testSetContentNode($content, string $expectedAsString, array $expectedNodes)
    {
        $node = new Node($content);

        $actualString = (string)$node;
        $this->assertEquals($expectedAsString, $actualString);

        $actualNodes = $node->getExtendedNodes();
        $this->assertEquals($expectedNodes, $actualNodes);
    }

    public function testSetContentWillThrowExceptionOnNonStringLikeContent()
    {
        $this->expectException(\Error::class);

        new Node(new \stdClass());
    }

    public function testGetNodes()
    {
        $nodeStub = $this->createMock(Node::class);

        $expectedResult = [$nodeStub];

        $sut = new Node(['foo', $nodeStub]);

        $actualResult = $sut->getNodes();

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testGetExtendedNodes()
    {
        $nodeStub = $this->createMock(Node::class);

        $expectedResult = [$nodeStub];

        $sut = new Node(['foo', $nodeStub]);

        $actualResult = $sut->getExtendedNodes();

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testForceGetNodes()
    {
        $nodeContent = 'foo';

        $nodeStub = $this->createMock(Node::class);
        $nodeStub2 = new Node($nodeContent);

        $expectedResult = [$nodeStub2, $nodeStub];

        $sut = new Node([$nodeContent, $nodeStub]);

        $actualResult = $sut->forceGetNodes();

        $this->assertNotSame($expectedResult, $actualResult);
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testConstructAddsIntents()
    {
        $expectedIntents = ['foo', 'bar'];

        $node = new Node(null, ...$expectedIntents);
        $this->assertEquals($expectedIntents, $node->getIntents());
    }

    public function testSetIntent()
    {
        $node = new Node(null, 'foo', 'bar');
        $this->assertTrue($node->hasIntent('foo'));
        $this->assertTrue($node->hasIntent('bar'));

        $node->setIntent('Foo', 'Bar');
        $this->assertFalse($node->hasIntent('foo'));
        $this->assertFalse($node->hasIntent('bar'));
        $this->assertTrue($node->hasIntent('Foo'));
        $this->assertTrue($node->hasIntent('Bar'));
    }

    public function testAddIntent()
    {
        $sut = new Node(null, 'foo', 'bar');
        $this->assertTrue($sut->hasIntent('foo'));
        $this->assertTrue($sut->hasIntent('bar'));

        $sut->addIntent('baz');
        $this->assertTrue($sut->hasIntent('foo'));
        $this->assertTrue($sut->hasIntent('bar'));
        $this->assertTrue($sut->hasIntent('baz'));
    }

    public function testSetTranslatorSetTranslatorOnNodes()
    {
        $embedded = new Node();
        $wrapper  = new Node($embedded);

        $translatorMock = $this->getMockBuilder(ITranslator::class)->getMock();

        $wrapper->setTranslator($translatorMock);

        $this->assertSame($translatorMock, $embedded->getTranslator());
    }

    /**
     * @return array[]
     */
    public function isMatchProvider(): array
    {
        $f1 = fn(INode $node) => $node->hasIntent('foo');
        $f2 = fn() => false;

        return [
            'empty'          => [null, null, [], true],
            'inode'          => [INode::class, null, [], true],
            'node'           => [Node::class, null, [], true],
            'nodetest'       => [NodeTest::class, null, [], false],
            'foo'            => [null, null, ['foo'], true],
            'bar'            => [null, null, ['bar'], true],
            'baz'            => [null, null, ['baz'], false],
            'foobar'         => [null, null, ['foo', 'bar'], true],
            'node-foobar'    => [Node::class, null, ['foo', 'bar'], true],
            'node-foobarbaz' => [Node::class, null, ['foo', 'bar', 'baz'], false],
            'f1'             => [null, $f1, [], true],
            'f2'             => [null, $f2, [], false],
        ];
    }

    /**
     * @dataProvider isMatchProvider
     *
     * @param string|null   $className
     * @param \Closure|null $matcher
     * @param string[]      $intents
     * @param bool          $expectedResult
     */
    public function testIsMatch(?string $className, ?Closure $matcher, array $intents, bool $expectedResult)
    {
        $sut = new Node('foo', 'foo', 'bar');

        $actualResult = $sut->isMatch($className, $matcher, ...$intents);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @dataProvider isMatchProvider
     *
     * @param string|null   $className
     * @param \Closure|null $matcher
     * @param string[]      $intents
     * @param bool          $expectedResult
     */
    public function testFindFindsItself(?string $className, ?Closure $matcher, array $intents, bool $expectedResult)
    {
        $sut = new Node('foo', 'foo', 'bar');

        $actualResult = $sut->find($className, $matcher, ...$intents);

        if ($expectedResult) {
            $this->assertSame($sut, $actualResult);
        } else {
            $this->assertNull($actualResult);
        }
    }

    /**
     * @return array[]
     */
    public function findTraversesNodesProvider(): array
    {
        $f1 = fn(INode $node) => $node->hasIntent('foo');
        $f2 = fn() => false;

        return [
            'foo'            => [null, null, ['foo'], true],
            'bar'            => [null, null, ['bar'], true],
            'baz'            => [null, null, ['baz'], false],
            'foobar'         => [null, null, ['foo', 'bar'], true],
            'node-foobar'    => [Node::class, null, ['foo', 'bar'], true],
            'node-foobarbaz' => [Node::class, null, ['foo', 'bar', 'baz'], false],
            'f1'             => [null, $f1, [], true],
            'f2'             => [null, $f2, [], false],
        ];
    }

    /**
     * @dataProvider findTraversesNodesProvider
     *
     * @param string|null   $className
     * @param \Closure|null $matcher
     * @param string[]      $intents
     * @param bool          $expectedResult
     */
    public function testFindTraversesNodes(?string $className, ?Closure $matcher, array $intents, bool $expectedResult)
    {
        $grandChild = new Node('foo', 'foo', 'bar');
        $child      = new Node([new Node(), '', $grandChild]);
        $sut        = new Node($child);

        $actualResult = $sut->find($className, $matcher, ...$intents);

        if ($expectedResult) {
            $this->assertSame($grandChild, $actualResult);
        } else {
            $this->assertNull($actualResult);
        }
    }

    /**
     * @return array[]
     */
    public function findAllProvider(): array
    {
        $f1 = fn(INode $node) => $node->hasIntent('foo');
        $f2 = fn() => false;

        return [
            'empty'          => [null, null, [], 4],
            'inode'          => [INode::class, null, [], 4],
            'node'           => [Node::class, null, [], 4],
            'nodetest'       => [NodeTest::class, null, [], 0],
            'foo'            => [null, null, ['foo'], 1],
            'bar'            => [null, null, ['bar'], 1],
            'baz'            => [null, null, ['baz'], 0],
            'foobar'         => [null, null, ['foo', 'bar'], 1],
            'node-foobar'    => [Node::class, null, ['foo', 'bar'], 1],
            'node-foobarbaz' => [Node::class, null, ['foo', 'bar', 'baz'], 0],
            'f1'             => [null, $f1, [], 1],
            'f2'             => [null, $f2, [], 0],
        ];
    }

    /**
     * @dataProvider findAllProvider
     *
     * @param string|null   $className
     * @param \Closure|null $matcher
     * @param string[]      $intents
     * @param int           $expectedLength
     */
    public function testFindAll(?string $className, ?Closure $matcher, array $intents, int $expectedLength)
    {
        $grandChild = new Node('foo', 'foo', 'bar');
        $child      = new Node([new Node(), '', $grandChild]);
        $sut        = new Node($child);

        $actualResult = $sut->findAll($className, $matcher, ...$intents);

        $this->assertSame($expectedLength, count($actualResult));
    }

    /**
     * @return array[]
     */
    public function findAllShallowProvider(): array
    {
        return [
            'empty-3'  => [3, null, null, [], 4],
            'empty-2'  => [2, null, null, [], 4],
            'empty-1'  => [1, null, null, [], 2],
            'empty-0'  => [0, null, null, [], 1],
            'empty--1' => [-1, null, null, [], 4],
            'foo-2'    => [2, null, null, ['foo'], 1],
            'foo-1'    => [1, null, null, ['foo'], 0],
        ];
    }

    /**
     * @dataProvider findAllShallowProvider
     *
     * @param int           $maxDepth
     * @param string|null   $className
     * @param \Closure|null $matcher
     * @param string[]      $intents
     * @param int           $expectedLength
     */
    public function testFindAllShallow(
        int $maxDepth,
        ?string $className,
        ?Closure $matcher,
        array $intents,
        int $expectedLength
    ) {
        $grandChild = new Node('foo', 'foo', 'bar');
        $child      = new Node([new Node(), '', $grandChild]);
        $sut        = new Node($child);

        $actualResult = $sut->findAllShallow($maxDepth, $className, $matcher, ...$intents);

        $this->assertSame($expectedLength, count($actualResult));
    }

    public function testToString()
    {
        $grandChild = new Node('foo', 'foo', 'bar');
        $child      = new Node([new Node(), '', $grandChild]);
        $sut        = new Node($child);

        $expectedResult = 'bar';

        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, ['foo' => 'bar']);

        $sut->setTranslator($translatorMock);

        $actualResult = (string)$sut;

        $this->assertSame($expectedResult, $actualResult);
    }
    public function toStringReturnsRawContentByDefaultProvider(): array
    {
        return [
            'INode[]' => [[new Node('foo')], 'foo'],
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
        $sut = $this->createCollectionStub($rawContent);

        $this->assertStringContainsString($expectedResult, (string)$sut);
    }

    /**
     * @return array
     */
    public function toStringCanReturnTranslatedContentProvider(): array
    {
        $translations = ['foo' => 'bar'];

        return [
            'INode[]' => [[new Node('foo')], $translations, 'bar'],
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

        $sut = $this->createCollectionStub($rawContent);

        $sut->setTranslator($translatorMock);

        $this->assertStringContainsString($expectedResult, (string)$sut);
    }

    public function testCountWithoutOffset(): void
    {
        $expectedResult = 2;

        $node1 = new Node('1');
        $node2 = new Node('2');

        $sut = $this->createCollectionStub();

        $sut[] = $node1;
        $sut[] = $node2;

        $this->assertSame($expectedResult, count($sut));
    }

    public function testCountWithExplicitOffset(): void
    {
        $expectedResult = 2;

        $node1 = new Node('1');
        $node2 = new Node('2');

        $sut = $this->createCollectionStub();

        $sut[0] = $node1;
        $sut[1] = $node2;

        $this->assertSame($expectedResult, count($sut));
    }

    public function testCountWithMixedOffset(): void
    {
        $node1 = new Node('1');
        $node2 = new Node('2');

        $expectedCount = 5;

        $sut = $this->createCollectionStub();

        $sut[]  = $node1;
        $sut[]  = $node1;
        $sut[1] = $node2;
        $sut[2] = $node1;
        $sut[3] = $node1;
        $sut[]  = $node1;

        $this->assertSame($expectedCount, count($sut));
    }

    public function testArrayAccessWithoutOffset(): void
    {
        $node1 = new Node('1');
        $node2 = new Node('2');

        $sut = $this->createCollectionStub();

        $sut[] = $node1;
        $sut[] = $node2;

        $this->assertSame($node1, $sut[0]);
        $this->assertSame($node2, $sut[1]);
    }

    public function testArrayAccessWithExplicitOffset(): void
    {
        $node1 = new Node('1');
        $node2 = new Node('2');

        $sut = $this->createCollectionStub();

        $sut[0] = $node1;
        $sut[1] = $node2;

        $this->assertSame($node1, $sut[0]);
        $this->assertSame($node2, $sut[1]);
    }

    public function testArrayAccessThrowExceptionWhenMadeDirty(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $node1 = new Node('1');

        $sut = $this->createCollectionStub();

        $sut[1] = $node1;
    }

    public function testArrayAccessWithMixedOffset(): void
    {
        $node1 = new Node('1');
        $node2 = new Node('2');

        $expectedNodes = [0 => $node1, 1 => $node2, 2 => $node1, 3 => $node1, 4 => $node1];

        $sut = $this->createCollectionStub();

        $sut[]  = $node1;
        $sut[]  = $node1;
        $sut[1] = $node2;
        $sut[2] = $node1;
        $sut[3] = $node1;
        $sut[]  = $node1;

        $this->assertEquals($expectedNodes, $sut->getExtendedNodes());
    }

    /**
     * @return array[]
     */
    public function contentFailureProvider(): array
    {
        return [
            'string wrapped'          => [['']],
            'non-node object wrapped' => [[new \StdClass()]],
            'node double wrapped'     => [[[new Node()]]],
        ];
    }

    /**
     * @dataProvider contentFailureProvider
     */
    public function testConstructFailure($item): void
    {
        $this->expectException(\AssertionError::class);

        $this->createCollectionStub($item);
    }

    /**
     * @dataProvider contentFailureProvider
     */
    public function testSetContentFailure($item): void
    {
        $this->expectException(\AssertionError::class);

        $sut = $this->createCollectionStub();

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
     */
    public function testArrayAccessFailureWithoutOffset($item): void
    {
        $this->expectException(\AssertionError::class);

        $sut = $this->createCollectionStub();

        $sut[] = $item;
    }

    /**
     * @dataProvider offsetSetFailureProvider
     */
    public function testArrayAccessFailureWithExplicitOffset($item): void
    {
        $this->expectException(\AssertionError::class);

        $sut = $this->createCollectionStub();

        $sut[] = $item;
    }

    public function testArrayAccessUnset(): void
    {
        $node1 = new Node('1');

        $sut = $this->createCollectionStub();

        $sut[] = $node1;

        $this->assertTrue($sut->offsetExists(0));

        unset($sut[0]);

        $this->assertfalse($sut->offsetExists(0));
    }

    public function testSetContentNull(): void
    {
        $expectedNodes = [];

        $sut = $this->createCollectionStub();

        $sut->setContent(null);

        $actualResult = $sut->getNodes();

        $this->assertEquals($expectedNodes, $actualResult);
    }

    public function testSetContentCollection(): void
    {
        $node1 = new Node('1');
        $node2 = new Node('2');

        $expectedNodes = [$node1];

        $sut = $this->createCollectionStub();

        $sut[]  = $node1;
        $sut[]  = $node1;
        $sut[1] = $node2;
        $sut[2] = $node1;
        $sut[3] = $node1;
        $sut[]  = $node1;

        $sut->setContent($node1);

        $actualResult = $sut->getNodes();

        $this->assertEquals($expectedNodes, $actualResult);
    }

    public function testIterator(): void
    {
        $node1 = new Node('1');
        $node2 = new Node('2');

        $expectedKeys  = [0, 1, 2, 3, 4];
        $expectedNodes = [$node1, $node2, $node1, $node1, $node1];

        $sut = $this->createCollectionStub();

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

    public function testGetRawContentReturnsNonTranslatedContent(): void
    {
        $this->assertTrue(true, 'No need to test getRawContent');
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
                [],
            ],
            'only-non-matching-content'      => [
                [$node2, $node3],
                $needle,
                [$needle, $node3],
                [$node2, $node3],
            ],
            'only-non-matching-content-deep' => [
                [$node2, $node3, new CollectionStub([$node2, $node3])],
                $needle,
                [$needle, $node3],
                [$node2, $node3, new CollectionStub([$node2, $node3])],
            ],
            'only-matching-content'          => [
                [$needle],
                $needle,
                [$node2, $node3],
                [$node2, $node3],
            ],
            'non-first-matching-content'     => [
                [$node2, $needle],
                $needle,
                [$node3, $node3],
                [$node2, $node3, $node3],
            ],
            'non-last-matching-content'      => [
                [$needle, $node2],
                $needle,
                [$node3, $node3],
                [$node3, $node3, $node2],
            ],
            'deep-first-matching-content'    => [
                [$node2, new CollectionStub([$node2, $needle])],
                $needle,
                [$node3, $node3],
                [$node2, new CollectionStub([$node2, $node3, $node3])],
            ],
        ];
    }

    /**
     * @dataProvider replaceProvider
     *
     * @param INode[] $content
     * @param INode   $nodeToFind
     * @param INode[] $nodesToInsert
     * @param INode[] $expectedNodes
     */
    public function testReplace(
        array $content,
        INode $nodeToFind,
        array $nodesToInsert,
        array $expectedNodes
    ): void {
        $sut = $this->createCollectionStub($content);

        $sut->replace($nodeToFind, ...$nodesToInsert);

        $this->assertEquals($expectedNodes, $sut->getNodes());
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
    ): void {
        $sut = $this->createCollectionStub();

        $sut->setIntent(...$intents);

        $actualResult = $sut->hasIntent($intentToCheck);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @param INode[]|INode|null $content
     *
     * @return CollectionStub
     */
    private function createCollectionStub($content = null): CollectionStub
    {
        return new CollectionStub($content);
    }
}
