<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Extra;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attributes;
use AbterPhp\Framework\Html\Helper\ArrayHelper;
use AbterPhp\Framework\TestDouble\Html\Component\StubAttributeFactory;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use PHPUnit\Framework\TestCase;

class HelpTest extends TestCase
{
    /**
     * @return array
     */
    public function renderProvider(): array
    {
        $attributes = StubAttributeFactory::createAttributes();
        $attributes->merge(new Attributes([Html5::ATTR_CLASS => [Help::CLASS_HELP_BLOCK]]));

        return [
            'simple'               => ['ABC', null, null, null, '<div class="help-block">ABC</div>'],
            'attributes'           => ['ABC', $attributes, [], null, "<div$attributes>ABC</div>"],
            'missing translations' => ['ABC', null, [], null, '<div class="help-block">ABC</div>'],
            'found translations'   => ['ABC', null, ['ABC' => 'CBA'], null, '<div class="help-block">CBA</div>'],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param string        $content
     * @param Attributes|null $attributes
     * @param string[]|null $translations
     * @param string|null   $tag
     * @param string        $expectedResult
     */
    public function testRender(
        string $content,
        ?Attributes $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ): void {
        $sut = $this->createElement($content, $attributes, $translations, $tag);

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;

        $this->assertSame($actualResult, $repeatedResult);
        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @param string        $content
     * @param Attributes|null $attributes
     * @param string[]|null $translations
     * @param string|null   $tag
     *
     * @return Help
     */
    private function createElement(
        string $content,
        ?Attributes $attributes,
        ?array $translations,
        ?string $tag
    ): Help {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $help = new Help($content, [], $attributes, $tag);

        $help->setTranslator($translatorMock);

        return $help;
    }
}
