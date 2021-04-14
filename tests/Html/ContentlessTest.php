<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use PHPUnit\Framework\TestCase;

class ContentlessTest extends TestCase
{
    public function testToStringIsEmptyByDefault(): void
    {
        $sut = $this->createNode();

        $this->assertStringContainsString('', (string)$sut);
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

        $sut->setAttribute(new Attribute('foo', 'bar'));

        $this->assertTrue($sut->hasAttribute('foo'));
    }

    /**
     * @return array[]
     */
    public function getAttributeProvider(): array
    {
        return [
            'empty'    => [[], ''],
            'simple'   => [['bar'], 'bar'],
            'repeated' => [['bar', 'bar'], 'bar'],
            'complex'  => [['foo', 'foo', 'bar', 'foo bar', 'bar'], 'foo bar foo bar'],
        ];
    }

    /**
     * @dataProvider getAttributeProvider
     *
     * @param array  $values
     * @param string $expectedResult
     */
    public function testGetAttributes(array $values, string $expectedResult): void
    {
        $key = 'foo';

        $sut = $this->createNode();

        $sut->setAttribute(new Attribute($key, ...$values));

        $this->assertEquals($expectedResult, $sut->getAttribute($key)->getValue());
    }

    public function testHasAttributeWithEmptyAttribute(): void
    {
        $sut = $this->createNode();

        $this->assertFalse($sut->hasAttribute('foo'));

        $sut->setAttribute(new Attribute('foo', null));

        $this->assertTrue($sut->hasAttribute('foo'));
    }

    public function testHasAttributeWithMissingAttributeSet(): void
    {
        $sut = $this->createNode();

        $this->assertFalse($sut->hasAttribute('foo'));

        $sut->setAttribute(new Attribute('foo'));

        $this->assertTrue($sut->hasAttribute('foo'));
    }

    /**
     * @dataProvider getAttributeProvider
     *
     * @param array       $values
     * @param string|null $expectedResult
     */
    public function testGetAttribute(array $values, ?string $expectedResult): void
    {
        $key = 'foo';

        $sut = $this->createNode();

        $sut->setAttribute(new Attribute($key, ...$values));

        $actualResult = $sut->getAttribute($key)->getValue();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testUnsetAttribute(): void
    {
        $key = 'foo';

        $sut = $this->createNode();

        $sut->setAttribute(new Attribute($key));

        $sut->getAttributes()->remove($key);

        $repeatedResult = $sut->getAttribute($key);

        $this->assertNull($repeatedResult);
    }

    public function testSetAttributesOverridesExistingAttributesSet(): void
    {
        $originalAttributes = new Attributes(['foo' => 'bar']);
        $newAttributes      = new Attributes(['bar' => 'baz']);
        $expectedResult     = clone $newAttributes;

        $sut = $this->createNode();
        $sut->setAttributes($originalAttributes);

        $sut->setAttributes($newAttributes);

        $actualResult = $sut->getAttributes();
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAddAttributesOverridesExistingAttributesSet(): void
    {
        $originalAttributes = new Attributes(['foo' => 'bar', 'bar' => 'foo']);
        $newAttributes      = new Attributes(['bar' => 'baz']);
        $expectedResult     = new Attributes(['foo' => 'bar', 'bar' => 'baz']);

        $sut = $this->createNode();
        $sut->setAttributes($originalAttributes);

        $sut->getAttributes()->replace($newAttributes);

        $actualResult = $sut->getAttributes();
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testSetAttributeOverridesExistingAttributeSet(): void
    {
        $key                = 'bar';
        $originalAttributes = new Attributes(['foo' => 'bar', 'bar' => 'foo']);
        $newAttributes      = new Attributes(['bar' => 'baz']);
        $expectedResult     = new Attributes(['foo' => 'bar', 'bar' => 'baz']);

        $sut = $this->createNode();
        $sut->setAttributes($originalAttributes);

        $sut->getAttributes()->replaceItem($newAttributes->getItem($key));

        $actualResult = $sut->getAttributes();
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testAppendToAttributeKeepsExistingAttributeSet(): void
    {
        $key                = 'bar';
        $newAttributeValue  = 'baz';
        $originalAttributes = new Attributes(['foo' => 'bar', 'bar' => 'foo']);
        $expectedResult     = 'foo baz';

        $sut = $this->createNode();
        $sut->setAttributes($originalAttributes);

        $sut->appendToAttribute($key, $newAttributeValue);

        $this->assertEquals($expectedResult, $sut->getAttribute($key)->getValue());
    }

    public function testAppendToClassKeepsExistingAttributeSet(): void
    {
        $originalAttributes = new Attributes(['foo' => 'bar', 'class' => 'foo']);
        $newClasses         = ['class1', 'class2'];
        $expectedResult     = new Attributes(['foo' => 'bar', 'class' => ['foo', 'class1', 'class2']]);

        $sut = $this->createNode();
        $sut->setAttributes($originalAttributes);

        $sut->appendToClass(...$newClasses);

        $actualResult = $sut->getAttributes();
        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testFind(): void
    {
        $this->expectException(\LogicException::class);

        $nodeToFind = new Node('');

        $sut = $this->createNode();

        $sut->find($nodeToFind);
    }

    /**
     * @return array<string,array>
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

    public function testFindFirstChild(): void
    {
        $this->expectException(\LogicException::class);

        $className = 'foo';
        $intents   = ['bar'];

        $sut = $this->createNode();

        $sut->findFirstChild($className, ...$intents);
    }

    public function testCollect(): void
    {
        $this->expectException(\LogicException::class);

        $className = 'foo';
        $intents   = ['bar'];
        $depth     = -1;

        $sut = $this->createNode();

        $sut->collect($className, $intents, $depth);
    }

    public function testSetContent(): void
    {
        $this->expectException(\LogicException::class);

        $sut = $this->createNode();

        $sut->setContent(12);
    }

    /**
     * @return Contentless
     */
    protected function createNode(): INode
    {
        return new Contentless();
    }
}
