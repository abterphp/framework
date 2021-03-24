<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;

class ComponentTest extends CollectionTest
{
    public function testDefaultToString(): void
    {
        $sut = $this->createNode();

        $this->assertSame('<span></span>', (string)$sut);
    }

    /**
     * @return array
     */
    public function toStringWithTranslationProvider(): array
    {
        return [
            ['AAA', ['AAA' => 'BBB'], '<span>BBB</span>'],
        ];
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
     * @return array
     */
    public function toStringReturnsRawContentByDefaultProvider(): array
    {
        return [
            'string'  => ['foo', '<span>foo</span>'],
            'INode'   => [new Node('foo'), '<span>foo</span>'],
            'INode[]' => [[new Node('foo')], '<span>foo</span>'],
        ];
    }

    /**
     * @return array
     */
    public function toStringCanReturnTranslatedContentProvider(): array
    {
        $translations = ['foo' => 'bar'];

        return [
            'string'  => ['foo', $translations, '<span>bar</span>'],
            'INode'   => [new Node('foo'), $translations, '<span>bar</span>'],
            'INode[]' => [[new Node('foo')], $translations, '<span>bar</span>'],
        ];
    }

    public function testSetIntentsCanOverwriteExistingIntents(): void
    {
        $sut = $this->createNode();

        $sut->setIntent('foo');
        $sut->setIntent('bar', 'baz');

        $this->assertEquals(['bar', 'baz'], $sut->getIntents());
    }

    public function testAddIntentAppendsToExistingIntents(): void
    {
        $sut = $this->createNode();

        $sut->setIntent('foo');
        $sut->addIntent('bar', 'baz');

        $this->assertEquals(['foo', 'bar', 'baz'], $sut->getIntents());
    }

    public function testHasAttributeWithNonEmptyAttribute(): void
    {
        $sut = $this->createNode();

        $this->assertFalse($sut->hasAttribute('foo'));

        $sut->setAttribute('foo', 'bar');

        $this->assertTrue($sut->hasAttribute('foo'));
    }

    /**
     * @return array
     */
    public function getAttributeProvider(): array
    {
        return [
            [null, null],
            ['bar', 'bar'],
            [['bar'], 'bar'],
            ['foo bar', 'foo bar'],
            ['foo foo bar', 'foo bar'],
            [['foo', 'foo', 'bar', 'foo bar', 'bar'], 'foo bar'],
        ];
    }

    /**
     * @dataProvider getAttributeProvider
     *
     * @param             $value
     * @param string|null $expectedResult
     */
    public function testGetAttributes($value, ?string $expectedResult): void
    {
        $key = 'foo';

        $sut = $this->createNode();

        $values = (array)$value;
        $sut->setAttribute($key, ...$values);

        $actualResult = $sut->getAttributes();

        $this->assertEquals([$key => $expectedResult], $actualResult);
    }

    public function testHasAttributeWithEmptyAttribute(): void
    {
        $sut = $this->createNode();

        $this->assertFalse($sut->hasAttribute('foo'));

        $sut->setAttribute('foo', null);

        $this->assertTrue($sut->hasAttribute('foo'));
    }

    public function testHasAttributeWithMissingAttributeSet(): void
    {
        $sut = $this->createNode();

        $this->assertFalse($sut->hasAttribute('foo'));

        $sut->setAttribute('foo');

        $this->assertTrue($sut->hasAttribute('foo'));
    }

    /**
     * @dataProvider getAttributeProvider
     *
     * @param             $value
     * @param string|null $expectedResult
     */
    public function testGetAttribute($value, ?string $expectedResult): void
    {
        $key = 'foo';

        $sut = $this->createNode();

        $values = (array)$value;
        $sut->setAttribute($key, ...$values);

        $actualResult = $sut->getAttribute($key);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @dataProvider getAttributeProvider
     *
     * @param             $value
     * @param string|null $expectedResult
     */
    public function testUnsetAttribute($value, ?string $expectedResult): void
    {
        $key = 'foo';

        $sut = $this->createNode();

        $values = (array)$value;
        $sut->setAttribute($key, ...$values);

        $actualResult = $sut->getAttribute($key);
        $this->assertEquals($expectedResult, $actualResult);

        $sut->unsetAttribute($key);

        $repeatedResult = $sut->getAttribute($key);

        $this->assertNull($repeatedResult);
    }

    public function testUnsetAttributeWorksOnNotSetAttributes(): void
    {
        $key = 'foo';

        $sut = $this->createNode();

        $sut->unsetAttribute($key);

        $result = $sut->getAttribute($key);

        $this->assertNull($result);
    }

    public function testSetAttributesOverridesExistingAttributesSet(): void
    {
        $originalAttributes = ['foo' => 'bar'];
        $newAttributes      = ['bar' => 'baz'];
        $expectedResult     = $newAttributes;

        $sut = $this->createNode();
        $sut->setAttributes($originalAttributes);

        $sut->setAttributes($newAttributes);

        $actualResult = $sut->getAttributes();
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAddAttributesOverridesExistingAttributesSet(): void
    {
        $originalAttributes = ['foo' => 'bar', 'bar' => 'foo'];
        $newAttributes      = ['bar' => 'baz'];
        $expectedResult     = ['foo' => 'bar', 'bar' => 'baz'];

        $sut = $this->createNode();
        $sut->setAttributes($originalAttributes);

        $sut->addAttributes($newAttributes);

        $actualResult = $sut->getAttributes();
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testSetAttributeOverridesExistingAttributeSet(): void
    {
        $key                = 'bar';
        $originalAttributes = ['foo' => 'bar', 'bar' => 'foo'];
        $newAttributes      = ['bar' => 'baz'];
        $expectedResult     = ['foo' => 'bar', 'bar' => 'baz'];

        $sut = $this->createNode();
        $sut->setAttributes($originalAttributes);

        $sut->setAttribute($key, $newAttributes[$key]);

        $actualResult = $sut->getAttributes();
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAppendToAttributeKeepsExistingAttributeSet(): void
    {
        $key                = 'bar';
        $originalAttributes = ['foo' => 'bar', 'bar' => 'foo'];
        $newAttributes      = ['bar' => 'baz'];
        $expectedResult     = ['foo' => 'bar', 'bar' => 'foo baz'];

        $sut = $this->createNode();
        $sut->setAttributes($originalAttributes);

        $sut->appendToAttribute($key, $newAttributes[$key]);

        $actualResult = $sut->getAttributes();
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAppendToClassKeepsExistingAttributeSet(): void
    {
        $originalAttributes = ['foo' => 'bar', 'class' => 'foo'];
        $newClasses         = ['class1', 'class2'];
        $expectedResult     = ['foo' => 'bar', 'class' => 'foo class1 class2'];

        $sut = $this->createNode();
        $sut->setAttributes($originalAttributes);

        $sut->appendToClass(...$newClasses);

        $actualResult = $sut->getAttributes();
        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function findProvider(): array
    {
        $node1 = new Node('1');
        $node2 = new Node('2');

        return [
            [[], $node1, null],
            [[$node2], $node1, null],
            [[$node1, $node2], $node1, 0],
            [[$node1, $node2], $node2, 1],
        ];
    }

    /**
     * @dataProvider findProvider
     *
     * @param INode[]  $content
     * @param INode    $nodeToFind
     * @param int|null $expectedResult
     */
    public function testFind(array $content, INode $nodeToFind, ?int $expectedResult): void
    {
        $sut = $this->createNode($content);

        $actualResult = $sut->find($nodeToFind);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @return array[]
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
        ];
    }

    /**
     * @return array
     */
    public function findFirstChildProvider(): array
    {
        $node0       = new Node('0');
        $component1  = (new Component('1'))->setIntent('foo');
        $component2  = (new Component('2'))->setIntent('bar');
        $component3  = (new Component('3'))->setIntent('foo', 'bar');
        $notFindable = new Collection((new Component('4'))->setIntent('foo', 'baz'));
        $content     = [$node0, $component1, $component2, $component3, $notFindable];

        return [
            'INode-no-intent'               => [$content, INode::class, [], $component1],
            'INode-foo-intent'              => [$content, INode::class, ['foo'], $component1],
            'INode-bar-intent'              => [$content, INode::class, ['bar'], $component2],
            'INode-foo-and-bar-intent'      => [$content, INode::class, ['foo', 'bar'], $component3],
            'IComponent-foo-intent'         => [$content, IComponent::class, ['foo'], $component1],
            'Component-foo-intent'          => [$content, Component::class, ['foo'], $component1],
            'fail-INode-baz-intent'         => [$content, INode::class, ['baz'], null],
            'fail-INode-foo-and-baz-intent' => [$content, INode::class, ['foo', 'baz'], null],
            'fail-Node-foo-intent'          => [$content, Node::class, ['foo'], null],
        ];
    }

    /**
     * @dataProvider findFirstChildProvider
     *
     * @param INode[]     $content
     * @param string|null $className
     * @param string[]    $intents
     * @param INode|null  $expectedResult
     */
    public function testFindFirstChild(array $content, ?string $className, array $intents, ?INode $expectedResult): void
    {
        $sut = $this->createNode($content);

        $actualResult = $sut->findFirstChild($className, ...$intents);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @return array[]
     */
    public function collectProvider(): array
    {
        $node0   = new Node('0');
        $node1   = new Node('1', ['foo']);
        $node2   = new Node('2', ['bar']);
        $node3   = new Node('3', ['foo', 'bar']);
        $coll1   = new Collection([$node1, $node2]);
        $coll2   = new Collection([$node3, $node0, $coll1, $node1]);
        $content = [$node1, $node0, $node2, $coll2, $node3];

        $level0Expected     = [$node1, $node0, $node2, $coll2, $node3];
        $level1Expected     = [$node1, $node0, $node2, $coll2, $node3, $node0, $coll1, $node1, $node3];
        $defaultExpected    = [$node1, $node0, $node2, $coll2, $node3, $node0, $coll1, $node1, $node2, $node1, $node3];
        $fooOnlyExpected    = [$node1, $node3, $node1, $node1, $node3];
        $fooBarOnlyExpected = [$node3, $node3];

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

    /**
     * @dataProvider collectProvider
     *
     * @param INode[]     $content
     * @param string|null $className
     * @param int         $depth
     * @param string[]    $intents
     * @param INode[]     $expectedResult
     */
    public function testCollect(
        array $content,
        ?string $className,
        int $depth,
        array $intents,
        array $expectedResult
    ): void {
        $sut = $this->createNode($content);

        $actualResult = $sut->collect($className, $intents, $depth);

        $this->assertEquals($expectedResult, $actualResult);
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

    /**
     * @param INode[]|INode|string|null $content
     *
     * @return Component
     */
    private function createNode($content = null): INode
    {
        return new Component($content);
    }
}
