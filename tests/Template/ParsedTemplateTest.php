<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

class ParsedTemplateTest extends \PHPUnit\Framework\TestCase
{
    /** @var ParsedTemplate - System Under Test */
    protected $sut;

    /** @var string */
    protected $type = 'foo';

    /** @var string */
    protected $identifier = 'bar';

    /** @var string[] */
    protected $attributes = ['body' => 'baz'];

    /** @var string[] */
    protected $occurences = ['one', 'two', 'three'];

    public function setUp()
    {
        $this->sut = new ParsedTemplate($this->type, $this->identifier, $this->attributes, $this->occurences);
    }

    public function testGetIdentifierRetrievesOriginallyProvidedType()
    {
        $actualResult = $this->sut->getType();

        $this->assertSame($this->type, $actualResult);
    }

    public function testGetIdentifierRetrievesOriginallyProvidedIdentifier()
    {
        $actualResult = $this->sut->getIdentifier();

        $this->assertSame($this->identifier, $actualResult);
    }

    public function testGetAttributesRetrievesOriginallyProvidedAttributes()
    {
        $actualResult = $this->sut->getAttributes();

        $this->assertSame($this->attributes, $actualResult);
    }

    public function testGetAttributeFindsAndRetrievesOriginallyProvidedAttributes()
    {
        $key = 'body';

        $actualResult = $this->sut->getAttribute($key);

        $this->assertSame($this->attributes['body'], $actualResult);
    }

    public function testGetAttributeFindsAndRetrievesNullIfAttributeIsNotSet()
    {
        $key = 'something';

        $actualResult = $this->sut->getAttribute($key);

        $this->assertNull($actualResult);
    }

    public function testGetOccurencesRetrievesOriginallyProvidedOccurencesByDefault()
    {
        $actualResult = $this->sut->getOccurences();

        $this->assertSame($this->occurences, $actualResult);
    }

    public function testGetOccurencesRetrievesAddedOccurences()
    {
        $expectedResult = ['one', 'two', 'three', 'four'];

        $newOccurence = 'four';

        $this->sut->addOccurence($newOccurence);

        $actualResult = $this->sut->getOccurences();

        $this->assertSame($expectedResult, $actualResult);
    }
}
