<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

use PHPUnit\Framework\TestCase;

class ParsedTemplateTest extends TestCase
{
    protected const TYPE = 'foo';
    protected const IDENTIFIER = 'bar';
    protected const ATTRIBUTES  = ['body' => 'baz'];
    protected const OCCURRENCES = ['one', 'two', 'three'];

    /** @var ParsedTemplate - System Under Test */
    protected ParsedTemplate $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new ParsedTemplate(static::TYPE, static::IDENTIFIER, static::ATTRIBUTES, static::OCCURRENCES);
    }

    public function testGetIdentifierRetrievesOriginallyProvidedType()
    {
        $actualResult = $this->sut->getType();

        $this->assertSame(static::TYPE, $actualResult);
    }

    public function testGetIdentifierRetrievesOriginallyProvidedIdentifier()
    {
        $actualResult = $this->sut->getIdentifier();

        $this->assertSame(static::IDENTIFIER, $actualResult);
    }

    public function testGetAttributesRetrievesOriginallyProvidedAttributes()
    {
        $actualResult = $this->sut->getAttributes();

        $this->assertSame(static::ATTRIBUTES, $actualResult);
    }

    public function testGetAttributeFindsAndRetrievesOriginallyProvidedAttributes()
    {
        $key = 'body';

        $actualResult = $this->sut->getAttribute($key);

        $this->assertSame(static::ATTRIBUTES['body'], $actualResult);
    }

    public function testGetAttributeFindsAndRetrievesNullIfAttributeIsNotSet()
    {
        $key = 'something';

        $actualResult = $this->sut->getAttribute($key);

        $this->assertNull($actualResult);
    }

    public function testGetOccurrencesRetrievesOriginallyProvidedOccurrencesByDefault()
    {
        $actualResult = $this->sut->getOccurrences();

        $this->assertSame(static::OCCURRENCES, $actualResult);
    }

    public function testGetOccurrencesRetrievesAddedOccurrences()
    {
        $expectedResult = ['one', 'two', 'three', 'four'];

        $newOccurrence = 'four';

        $this->sut->addOccurrence($newOccurrence);

        $actualResult = $this->sut->getOccurrences();

        $this->assertSame($expectedResult, $actualResult);
    }
}
