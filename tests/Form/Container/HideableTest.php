<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Container;

use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use PHPUnit\Framework\TestCase;

class HideableTest extends TestCase
{
    public function testRender(): void
    {
        $expectedResult = 'foo';

        $sut = new Hideable($expectedResult);

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;

        $this->assertStringContainsString($expectedResult, $actualResult);
        $this->assertSame($actualResult, $repeatedResult);
    }

    public function testRenderContainsHiderBtn(): void
    {
        $expectedResult = 'foo';

        $sut = new Hideable($expectedResult);

        $actualResult = (string)$sut;

        $this->assertStringContainsString((string)$sut->getHiderBtn(), $actualResult);
    }

    public function testGetExtendedNodesIncludesHiderBtn(): void
    {
        $expectedResult = 'foo';

        $sut = new Hideable($expectedResult);

        $actualResult = $sut->getExtendedNodes();

        $this->assertContains($sut->getHiderBtn(), $actualResult);
    }

    public function testSetTranslatorSetsTranslatorOfHiderBtn(): void
    {
        $hiderBtnLabel  = 'foo';
        $expectedResult = 'bar';

        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, [$hiderBtnLabel => $expectedResult]);

        $sut = new Hideable($expectedResult);

        $sut->setTranslator($translatorMock);

        $this->assertStringContainsString($expectedResult, (string)$sut->getHiderBtn());
    }

    public function testSetTemplateChangesRender(): void
    {
        $expectedResult = '==||==';

        $sut = new Hideable('foo');

        $sut->setTemplate($expectedResult);

        $actualResult = (string)$sut;

        $this->assertStringContainsString($expectedResult, $actualResult);
    }
}
