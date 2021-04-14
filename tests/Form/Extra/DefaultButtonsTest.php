<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Extra;

use AbterPhp\Framework\Html\Attributes;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use PHPUnit\Framework\TestCase;

class DefaultButtonsTest extends TestCase
{
    /**
     * @return array[]
     */
    public function renderProvider(): array
    {
        $simpleExpected = [
            "<div><button name=\"next\" type=\"submit\" value=\"\">framework:save</button>",
            "<button name=\"next\" type=\"submit\" value=\"back\">framework:saveAndBack</button>",
            "<button name=\"next\" type=\"submit\" value=\"edit\">framework:saveAndEdit</button>",
            "<button name=\"next\" type=\"submit\" value=\"create\">framework:saveAndCreate</button>",
            "<a href=\"/url\">framework:backToGrid</a></div>",
        ];

        return [
            'simple' => ['/url', null, [], null, implode("\n", $simpleExpected)],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param string          $showUrl
     * @param Attributes|null $attributes
     * @param string[]        $translations
     * @param string|null     $tag
     * @param string          $expected
     */
    public function testRender(
        string $showUrl,
        ?Attributes $attributes,
        array $translations,
        ?string $tag,
        string $expected
    ): void {
        $sut = $this->createElement($showUrl, $attributes, $translations, $tag);

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;
        $this->assertSame($actualResult, $repeatedResult);

        $this->assertSame($expected, $actualResult);
    }

    /**
     * @param string          $showUrl
     * @param Attributes|null $attributes
     * @param string[]        $translations
     * @param string|null     $tag
     *
     * @return DefaultButtons
     */
    private function createElement(
        string $showUrl,
        ?Attributes $attributes,
        array $translations,
        ?string $tag
    ): DefaultButtons {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $defaultButtons = new DefaultButtons(null, [], $attributes, $tag);

        $defaultButtons->setTranslator($translatorMock);
        $defaultButtons
            ->addSave()
            ->addSaveAndBack()
            ->addSaveAndEdit()
            ->addSaveAndCreate()
            ->addBackToGrid($showUrl);

        return $defaultButtons;
    }
}
