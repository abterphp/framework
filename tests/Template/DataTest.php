<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

use PHPUnit\Framework\TestCase;

class DataTest extends TestCase
{
    public const IDENTIFIER = 'foo';
    public const VARS       = ['body' => 'bar'];
    public const TEMPLATES  = ['one', 'two', 'three'];

    /** @var Data - System Under Test */
    protected Data $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new Data(static::IDENTIFIER, static::VARS, static::TEMPLATES);
    }

    public function testGetIdentifierRetrievesOriginallyProvidedIdentifierByDefault(): void
    {
        $actualResult = $this->sut->getIdentifier();

        $this->assertSame(static::IDENTIFIER, $actualResult);
    }

    public function testGetIdentifierRetrievesLastSetIdentifier(): void
    {
        $expectedResult = 'foo';

        $this->sut->setIdentifier($expectedResult);

        $actualResult = $this->sut->getIdentifier();

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testGetVarsRetrievesOriginallyProvidedVarsByDefault(): void
    {
        $actualResult = $this->sut->getVars();

        $this->assertSame(static::VARS, $actualResult);
    }

    public function testGetVarsRetrievesLastSetVars(): void
    {
        $expectedResult = ['foo'];

        $this->sut->setVars($expectedResult);

        $actualResult = $this->sut->getVars();

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testGetTemplatesRetrievesOriginallyProvidedTemplatesByDefault(): void
    {
        $actualResult = $this->sut->getTemplates();

        $this->assertSame(static::TEMPLATES, $actualResult);
    }

    public function testGetTemplatesRetrievesLastSetTemplates(): void
    {
        $expectedResult = ['foo'];

        $this->sut->setTemplates($expectedResult);

        $actualResult = $this->sut->getTemplates();

        $this->assertSame($expectedResult, $actualResult);
    }
}
