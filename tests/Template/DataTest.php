<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

class DataTest extends \PHPUnit\Framework\TestCase
{
    /** @var Data - System Under Test */
    protected $sut;

    /** @var string */
    protected $identifier = 'foo';

    /** @var string[] */
    protected $vars = ['body' => 'bar'];

    /** @var string[] */
    protected $templates = ['one', 'two', 'three'];

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new Data($this->identifier, $this->vars, $this->templates);
    }

    public function testGetIdentifierRetrievesOriginallyProvidedIdentifierByDefault()
    {
        $actualResult = $this->sut->getIdentifier();

        $this->assertSame($this->identifier, $actualResult);
    }

    public function testGetIdentifierRetrievesLastSetIdentifier()
    {
        $expectedResult = 'foo';

        $this->sut->setIdentifier($expectedResult);

        $actualResult = $this->sut->getIdentifier();

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testGetVarsRetrievesOriginallyProvidedVarsByDefault()
    {
        $actualResult = $this->sut->getVars();

        $this->assertSame($this->vars, $actualResult);
    }

    public function testGetVarsRetrievesLastSetVars()
    {
        $expectedResult = ['foo'];

        $this->sut->setVars($expectedResult);

        $actualResult = $this->sut->getVars();

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testGetTemplatesRetrievesOriginallyProvidedTemplatesByDefault()
    {
        $actualResult = $this->sut->getTemplates();

        $this->assertSame($this->templates, $actualResult);
    }

    public function testGetTemplatesRetrievesLastSetTemplates()
    {
        $expectedResult = ['foo'];

        $this->sut->setTemplates($expectedResult);

        $actualResult = $this->sut->getTemplates();

        $this->assertSame($expectedResult, $actualResult);
    }
}
