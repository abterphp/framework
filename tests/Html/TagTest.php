<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use AbterPhp\Framework\TestCase\Html\NodeTestCase;

class TagTest extends NodeTestCase
{
    public function testDefaultToString()
    {
        $sut = $this->createNode();

        $this->assertSame('<div></div>', (string)$sut);
    }

    /**
     * @return array
     */
    public function toStringWithTranslationProvider(): array
    {
        return [
            ['AAA', ['AAA' => 'BBB'], '<div>BBB</div>'],
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
            'fail-IComponent-foo-intent'    => [IComponent::class, ['foo'], false],
            'fail-Component-foo-intent'     => [Component::class, ['foo'], false],
            'fail-INode-baz-intent'         => [INode::class, ['baz'], false],
            'fail-INode-foo-and-baz-intent' => [INode::class, ['foo', 'baz'], false],
            'Node-foo-intent'               => [Node::class, ['foo'], true],
            'Tag-foo-intent'                => [Tag::class, ['foo'], true],
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

    public function testUnsetAttributeWorksIfAttributeIsNotSet()
    {
        $key   = 'foo';
        $value = 'bar';

        $sut = $this->createNode();

        $sut->setAttribute($key, $value);

        $sut->unsetAttribute($key);

        $actualResult = $sut->getAttribute($key);

        $this->assertNull($actualResult);
    }

    public function testUnsetAttributeValueWorksIfAttributeIsNotSet()
    {
        $key   = 'foo';
        $value = 'bar';

        $sut = $this->createNode();

        $sut->unsetAttributeValue($key, $value);

        $actualResult = $sut->getAttribute($key);

        $this->assertNull($actualResult);
    }

    public function testUnsetAttributeValueWorksIfAttributeIsSet()
    {
        $key   = 'foo';
        $value = 'bar';

        $sut = $this->createNode();

        $sut->setAttribute($key, $value);

        $sut->unsetAttributeValue($key, $value);

        $actualResult = $sut->getAttribute($key);

        $this->assertNull($actualResult);
    }

    public function testUnsetAttributeValueWorksIfAttributeIsSetButValueIsNot()
    {
        $key   = 'foo';
        $value1 = 'bar';
        $value2 = 'baz';

        $sut = $this->createNode();

        $sut->setAttribute($key, $value1);

        $sut->unsetAttributeValue($key, $value2);

        $actualResult = $sut->getAttribute($key);

        $this->assertSame($value1, $actualResult);
    }

    /**
     * @param INode[]|INode|string|null $content
     *
     * @return Tag
     */
    protected function createNode($content = null): INode
    {
        return new Tag($content);
    }
}
