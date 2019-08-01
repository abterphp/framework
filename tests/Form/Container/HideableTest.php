<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Container;

use AbterPhp\Framework\I18n\MockTranslatorFactory;

class HideableTest extends \PHPUnit\Framework\TestCase
{
    public function testRender()
    {
        $expectedResult = 'foo';

        $sut = new Hideable($expectedResult);

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;

        $this->assertStringContainsString($expectedResult, $actualResult);
        $this->assertSame($actualResult, $repeatedResult);
    }

    public function testRenderContainsHiderBtn()
    {
        $expectedResult = 'foo';

        $sut = new Hideable($expectedResult);

        $actualResult = (string)$sut;

        $this->assertStringContainsString((string)$sut->getHiderBtn(), $actualResult);
    }

    public function testGetExtendedNodesIncludesHiderBtn()
    {
        $expectedResult = 'foo';

        $sut = new Hideable($expectedResult);

        $actualResult = $sut->getExtendedNodes();

        $this->assertContains($sut->getHiderBtn(), $actualResult);
    }

    public function testSetTranslatorSetsTranslatorOfHiderBtn()
    {
        $hiderBtnLabel  = 'foo';
        $expectedResult = 'bar';

        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, [$hiderBtnLabel => $expectedResult]);

        $sut = new Hideable($expectedResult);

        $sut->setTranslator($translatorMock);

        $this->assertStringContainsString($expectedResult, (string)$sut->getHiderBtn());
    }

    public function testSetTemplateChangesRender()
    {
        $expectedResult = '==||==';

        $sut = new Hideable('foo');

        $sut->setTemplate($expectedResult);

        $actualResult = (string)$sut;

        $this->assertStringContainsString($expectedResult, $actualResult);
    }
}
