<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use PHPUnit\Framework\TestCase;

class ContentlessTest extends TestCase
{
    public function testToStringIsEmptyByDefault()
    {
        $sut = $this->createNode();

        $this->assertStringContainsString('', (string)$sut);
    }

    public function testSetIntentsCanOverwriteExistingIntents()
    {
        $sut = $this->createNode();

        $sut->setIntent('foo');
        $sut->setIntent('bar', 'baz');

        $this->assertEquals(['bar', 'baz'], $sut->getIntents());
    }

    public function testAddIntentAppendsToExistingIntents()
    {
        $sut = $this->createNode();

        $sut->setIntent('foo');
        $sut->addIntent('bar', 'baz');

        $this->assertEquals(['foo', 'bar', 'baz'], $sut->getIntents());
    }

    public function testHasAttributeWithNonEmptyAttribute()
    {
        $sut = $this->createNode();

        $this->assertFalse($sut->hasAttribute('foo'));

        $sut->setAttribute('foo', 'bar');

        $this->assertTrue($sut->hasAttribute('foo'));
    }

    /**
     * @return array[]
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
    public function testGetAttributes($value, ?string $expectedResult)
    {
        $key = 'foo';

        $sut = $this->createNode();

        $values = (array)$value;
        $sut->setAttribute($key, ...$values);

        $actualResult = $sut->getAttributes();

        $this->assertEquals([$key => $expectedResult], $actualResult);
    }

    public function testHasAttributeWithEmptyAttribute()
    {
        $sut = $this->createNode();

        $this->assertFalse($sut->hasAttribute('foo'));

        $sut->setAttribute('foo', null);

        $this->assertTrue($sut->hasAttribute('foo'));
    }

    public function testHasAttributeWithMissingAttributeSet()
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
    public function testGetAttribute($value, ?string $expectedResult)
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
    public function testUnsetAttribute($value, ?string $expectedResult)
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

    public function testSetAttributesOverridesExistingAttributesSet()
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

    public function testAddAttributesOverridesExistingAttributesSet()
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

    public function testSetAttributeOverridesExistingAttributeSet()
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

    public function testAppendToAttributeKeepsExistingAttributeSet()
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

    public function testAppendToClassKeepsExistingAttributeSet()
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

    public function testFind()
    {
        $this->expectException(\LogicException::class);

        $nodeToFind = new Node('');

        $sut = $this->createNode();

        $sut->find($nodeToFind);
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
        ];
    }

    /**
     * @dataProvider isMatchProvider
     *
     * @param string|null $className
     * @param string[]    $intents
     * @param bool        $expectedResult
     */
    public function testIsMatch(?string $className, array $intents, bool $expectedResult)
    {
        $sut = $this->createNode();
        $sut->setIntent('foo', 'bar');

        $actualResult = $sut->isMatch($className, ...$intents);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testFindFirstChild()
    {
        $this->expectException(\LogicException::class);

        $className = 'foo';
        $intents   = ['bar'];

        $sut = $this->createNode();

        $sut->findFirstChild($className, ...$intents);
    }

    public function testCollect()
    {
        $this->expectException(\LogicException::class);

        $className = 'foo';
        $intents   = ['bar'];
        $depth     = -1;

        $sut = $this->createNode();

        $sut->collect($className, $intents, $depth);
    }

    public function testSetContent()
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
