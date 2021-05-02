<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Helper\Attributes;
use PHPUnit\Framework\TestCase;

class TagTest extends TestCase
{
    public function testConstructSetsTag(): void
    {
        $expectedResult = '<article></article>';

        $sut = new Tag(null, [], null, Html5::TAG_ARTICLE);

        $actualResult = (string)$sut;

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testSetTag(): void
    {
        $expectedResult = '<article></article>';

        $sut = new Tag();

        $sut->setTag(Html5::TAG_ARTICLE);

        $actualResult = (string)$sut;

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testResetTag(): void
    {
        $expectedResult = '<div></div>';

        $sut = new Tag();

        $sut->setTag(Html5::TAG_ARTICLE)->resetTag();

        $actualResult = (string)$sut;

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testConstructSetsAttributes(): void
    {
        $expectedResult = '<div foo="bar"></div>';
        $attributes     = Attributes::fromArray(['foo' => 'bar']);

        $sut = new Tag(null, [], $attributes);

        $actualResult = (string)$sut;

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testSetAttributes(): void
    {
        $expectedResult = '<div foo="bar"></div>';
        $attributes     = Attributes::fromArray(['foo' => 'bar']);

        $sut = new Tag();
        $sut->setAttributes($attributes);

        $actualResult = (string)$sut;

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testGetAttribute(): void
    {
        $expectedResult = 'foo="bar"';
        $attributes     = Attributes::fromArray(['foo' => 'bar']);

        $sut = new Tag();
        $sut->setAttribute($attributes['foo']);

        $actualResult = $sut->getAttribute('foo');

        $this->assertSame($expectedResult, (string)$actualResult);
    }

    public function testGetAttributeReturnsNullWhenAttributeDoesNotExist(): void
    {
        $sut = new Tag();

        $actualResult = $sut->getAttribute('foo');

        $this->assertNull($actualResult);
    }

    public function testHasAttribute(): void
    {
        $attributes = Attributes::fromArray(['foo' => 'bar']);

        $sut = new Tag();
        $sut->setAttribute($attributes['foo']);

        $actualResult = $sut->hasAttribute('foo');

        $this->assertTrue($actualResult);
    }

    public function testRemoveAttribute(): void
    {
        $attributes = Attributes::fromArray(['foo' => 'bar', 'bar' => 'baz']);

        $sut = new Tag(null, [], $attributes);
        $sut->removeAttribute('foo');

        $actualResult = $sut->getAttributes();

        $this->assertEquals(['bar'], array_keys($actualResult));
    }

    public function testAppendToAttributeAsNew(): void
    {
        $expectedResult = 'foo="bar baz"';

        $sut = new Tag();
        $sut->appendToAttribute('foo', 'bar', 'baz');

        $actualResult = $sut->getAttribute('foo');
        $this->assertEquals($expectedResult, (string)$actualResult);
    }

    public function testAppendToClassAsNew(): void
    {
        $expectedResult = 'class="bar baz"';

        $sut = new Tag();
        $sut->appendToClass('bar', 'baz');

        $actualResult = $sut->getAttribute(Html5::ATTR_CLASS);
        $this->assertEquals($expectedResult, (string)$actualResult);
    }
}
