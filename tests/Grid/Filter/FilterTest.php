<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Grid\Filter;

use AbterPhp\Framework\Html\ITemplater;
use AbterPhp\Framework\I18n\MockTranslatorFactory;

abstract class FilterTest extends \PHPUnit\Framework\TestCase
{
    public function testSetTemplateCanOverrideContent()
    {
        $template = '--||--';

        $sut = $this->createFilter();
        if (!($sut instanceof ITemplater)) {
            $this->markTestSkipped($sut);
        }

        $sut->setTemplate($template);

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;

        $this->assertContains($template, $actualResult);
        $this->assertContains($actualResult, $repeatedResult);
    }

    public function testGetExtendedNodesContainsLabelAndWrapper()
    {
        $sut = $this->createFilter();

        $actualResult = $sut->getExtendedNodes(-1);

        $this->assertContains($sut->getLabel(), $actualResult);
        $this->assertContains($sut->getWrapper(), $actualResult);
    }

    public function testSetTranslatorSetsTranslators()
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
