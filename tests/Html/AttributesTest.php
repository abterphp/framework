<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use PHPUnit\Framework\TestCase;

class AttributesTest extends TestCase
{
    /** @var Attributes - System Under Test */
    protected Attributes $sut;

    public function setUp(): void
    {
        $this->sut = new Attributes(['foo' => ['bar baz'], 'bar' => ['baz', 'quix']]);
    }

    public function testMerge(): void
    {
        $expectedResult = ' foo="bar baz foo bar" bar="baz quix" baz="foo bar"';

        $attributes = new Attributes(['baz' => ['foo', 'bar'], 'foo' => ['foo', 'bar']]);

        $this->sut->merge($attributes);

        $actualResult = (string)$this->sut;

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testReplace(): void
    {
        $expectedResult = ' foo="foo bar" bar="baz quix" baz="foo bar"';

        $attributes = new Attributes(['baz' => ['foo', 'bar'], 'foo' => ['foo', 'bar']]);

        $this->sut->replace($attributes);

        $actualResult = (string)$this->sut;

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testRemove(): void
    {
        $expectedResult = ' foo="bar baz"';

        $this->sut->remove('bar');

        $actualResult = (string)$this->sut;

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testIsEqualToNullIfEmpty(): void
    {
        $sut = new Attributes();

        $this->assertTrue($sut->isEqual(null));
    }

    public function testIsNotEqualToNullByDefault(): void
    {
        $this->assertFalse($this->sut->isEqual(null));
    }

    public function testIsNotEqualToAttributesWithDifference(): void
    {
        $attributesB = new Attributes(['foo' => ['bar'], 'bar' => ['baz', 'quix']]);

        $this->assertFalse($this->sut->isEqual($attributesB));
    }

    public function testIsEqualToOtherAttributesWithSameAttributeKeyValuePairs(): void
    {
        $attributesB = new Attributes(['foo' => ['bar baz'], 'bar' => ['baz', 'quix']]);

        $this->assertTrue($this->sut->isEqual($attributesB));
    }
}
