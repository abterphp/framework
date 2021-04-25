<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

use AbterPhp\Framework\Html\Helper\Attributes;
use PHPUnit\Framework\TestCase;

class ParsedTemplateTest extends TestCase
{
    protected const TYPE        = 'foo';
    protected const IDENTIFIER  = 'bar';
    protected const ATTRIBUTES  = ['body' => ['baz']];
    protected const OCCURRENCES = ['one', 'two', 'three'];

    /** @var ParsedTemplate - System Under Test */
    protected ParsedTemplate $sut;

    public function setUp(): void
    {
        parent::setUp();

        $attributes = Attributes::fromArray(static::ATTRIBUTES);

        $this->sut = new ParsedTemplate(
            static::TYPE,
            static::IDENTIFIER,
            $attributes,
            static::OCCURRENCES
        );
    }

    public function testGetIdentifierRetrievesOriginallyProvidedType(): void
    {
        $actualResult = $this->sut->getType();

        $this->assertSame(static::TYPE, $actualResult);
    }

    public function testGetIdentifierRetrievesOriginallyProvidedIdentifier(): void
    {
        $actualResult = $this->sut->getIdentifier();

        $this->assertSame(static::IDENTIFIER, $actualResult);
    }

    public function testGetAttributesRetrievesOriginallyProvidedAttributes(): void
    {
        $attributes   = Attributes::fromArray(static::ATTRIBUTES);
        $actualResult = $this->sut->getAttributes();

        $this->assertEquals($attributes, $actualResult);
    }

    public function testGetAttributeFindsAndRetrievesOriginallyProvidedAttributes(): void
    {
        $key = 'body';

        $actualResult = $this->sut->getAttributeValue($key);

        $this->assertSame(implode(' ', static::ATTRIBUTES['body']), $actualResult);
    }

    public function testGetAttributeFindsAndRetrievesNullIfAttributeIsNotSet(): void
    {
        $key = 'something';

        $actualResult = $this->sut->getAttributeValue($key);

        $this->assertNull($actualResult);
    }

    public function testGetOccurrencesRetrievesOriginallyProvidedOccurrencesByDefault(): void
    {
        $actualResult = $this->sut->getOccurrences();

        $this->assertSame(static::OCCURRENCES, $actualResult);
    }

    public function testGetOccurrencesRetrievesAddedOccurrences(): void
    {
        $expectedResult = ['one', 'two', 'three', 'four'];

        $newOccurrence = 'four';

        $this->sut->addOccurrence($newOccurrence);

        $actualResult = $this->sut->getOccurrences();

        $this->assertSame($expectedResult, $actualResult);
    }
}
