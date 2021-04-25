<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Filter;

use AbterPhp\Framework\Html\ITemplater;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use PHPUnit\Framework\TestCase;

abstract class FilterTest extends TestCase
{
    public function testSetTemplateCanOverrideContent(): void
    {
        $template = '--||--';

        $sut = $this->createFilter();
        if (!($sut instanceof ITemplater)) {
            $this->markTestSkipped();
        }

        $sut->setTemplate($template);

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;

        $this->assertStringContainsString($template, $actualResult);
        $this->assertStringContainsString($actualResult, $repeatedResult);
    }

    public function testGetExtendedNodesContainsLabelAndWrapper(): void
    {
        $sut = $this->createFilter();

        $actualResult = $sut->getExtendedNodes();

        $this->assertContains($sut->getLabel(), $actualResult);
        $this->assertContains($sut->getWrapper(), $actualResult);
    }

    public function testSetTranslatorSetsTranslators(): void
    {
        $mockTranslator = MockTranslatorFactory::createSimpleTranslator($this, []);

        $sut = $this->createFilter();

        $sut->setTranslator($mockTranslator);

        $this->assertNotNull($sut->getLabel()->getTranslator());
        $this->assertNotNull($sut->getWrapper()->getTranslator());
    }

    /**
     * @param string $inputName
     * @param string $fieldName
     *
     * @return IFilter
     */
    abstract protected function createFilter($inputName = 'foo', $fieldName = 'foo_field'): IFilter;
}
