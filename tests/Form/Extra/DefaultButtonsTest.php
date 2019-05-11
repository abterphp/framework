<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Extra;

use AbterPhp\Framework\I18n\MockTranslatorFactory;

class DefaultButtonsTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return array
     */
    public function renderProvider()
    {
        $simpleExpected = [
            "<div><button name=\"next\" type=\"submit\" value=\"back\">framework:saveAndBack</button>",
            "<button name=\"next\" type=\"submit\" value=\"edit\">framework:saveAndEdit</button>",
            "<button name=\"next\" type=\"submit\" value=\"create\">framework:saveAndCreate</button>",
            "<a href=\"/url\">framework:backToGrid</a></div>",
        ];

        return [
            'simple' => ['/url', [], [], null, implode("\n", $simpleExpected)],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param string      $showUrl
     * @param array       $attributes
     * @param string[]    $translations
     * @param string|null $tag
     * @param string      $expected
     */
    public function testRender(
        string $showUrl,
        array $attributes,
        array $translations,
        ?string $tag,
        string $expected
    ) {
        $sut = $this->createElement($showUrl, $attributes, $translations, $tag);

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;
        $this->assertSame($actualResult, $repeatedResult);

        $this->assertSame($expected, $actualResult);
    }

    /**
     * @param string      $showUrl
     * @param array       $attributes
     * @param string[]    $translations
     * @param string|null $tag
     *
     * @return DefaultButtons
     */
    private function createElement(
        string $showUrl,
        array $attributes,
        array $translations,
        ?string $tag
    ): DefaultButtons {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $defaultButtons = new DefaultButtons(null, [], $attributes, $tag);

        $defaultButtons
            ->setTranslator($translatorMock)
            ->addSaveAndBack()
            ->addSaveAndEdit()
            ->addSaveAndCreate()
            ->addBackToGrid($showUrl);

        return $defaultButtons;
    }
}
