<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Component;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Component;
use AbterPhp\Framework\Html\Helper\ArrayHelper;
use AbterPhp\Framework\Html\IComponent;
use AbterPhp\Framework\TestDouble\Html\Component\StubAttributeFactory;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use PHPUnit\Framework\TestCase;

class ButtonWithIconTest extends TestCase
{
    public function renderProvider(): array
    {
        $attr = StubAttributeFactory::createAttributes();
        $str  = ArrayHelper::toAttributes($attr);

        $text = new Component('A', [], [], Html5::TAG_B);
        $icon = new Component('B', [], [], Html5::TAG_I);

        $textStr  = '<b>A</b>';
        $iconStr  = '<i>B</i>';
        $transStr = '<b>Z</b>';

        return [
            'simple'               => [$text, $icon, [], [], null, null, "<button>$iconStr $textStr</button>"],
            'with attributes'      => [$text, $icon, [], $attr, null, null, "<button$str>$iconStr $textStr</button>"],
            'missing translations' => [$text, $icon, [], [], [], null, "<button>$iconStr $textStr</button>"],
            'custom tag'           => [$text, $icon, [], [], null, 'mytag', "<mytag>$iconStr $textStr</mytag>"],
            'with translations'    => [$text, $icon, [], [], ['A' => 'Z'], null, "<button>$iconStr $transStr</button>"],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param IComponent  $text
     * @param IComponent  $icon
     * @param array       $intents
     * @param array       $attributes
     * @param array|null  $translations
     * @param string|null $tag
     * @param string      $expectedResult
     */
    public function testRender(
        IComponent $text,
        IComponent $icon,
        array $intents,
        array $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ) {
        $sut = $this->createElement($text, $icon, $intents, $attributes, $translations, $tag);

        $actualResult1 = (string)$sut;
        $actualResult2 = (string)$sut;

        $this->assertSame($expectedResult, $actualResult1);
        $this->assertSame($expectedResult, $actualResult2);
    }

    public function testSetTemplateChangesToString()
    {
        $template = '--||--';

        $text = new Component('A', [], [], Html5::TAG_B);
        $icon = new Component('B', [], [], Html5::TAG_I);

        $sut = new ButtonWithIcon($text, $icon);

        $sut->setTemplate($template);

        $actualResult = (string)$sut;

        $this->assertStringContainsString($template, $actualResult);
    }

    public function testGetTextRetrievesText()
    {
        $text = new Component('A', [], [], Html5::TAG_B);
        $icon = new Component('B', [], [], Html5::TAG_I);

        $sut = new ButtonWithIcon($text, $icon);

        $actualResult = $sut->getText();

        $this->assertSame($text, $actualResult);
    }

    public function testGetIconRetrievesIcon()
    {
        $text = new Component('A', [], [], Html5::TAG_B);
        $icon = new Component('B', [], [], Html5::TAG_I);

        $sut = new ButtonWithIcon($text, $icon);

        $actualResult = $sut->getIcon();

        $this->assertSame($icon, $actualResult);
    }

    /**
     * @param IComponent  $text
     * @param IComponent  $icon
     * @param array       $intents
     * @param array       $attributes
     * @param array|null  $translations
     * @param string|null $tag
     *
     * @return ButtonWithIcon
     */
    protected function createElement(
        IComponent $text,
        IComponent $icon,
        array $intents,
        array $attributes,
        ?array $translations,
        ?string $tag
    ): ButtonWithIcon {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $button = new ButtonWithIcon($text, $icon, $intents, $attributes, $tag);

        $button->setTranslator($translatorMock);

        return $button;
    }
}
