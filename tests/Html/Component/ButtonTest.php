<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Component;

use AbterPhp\Framework\Html\Helper\ArrayHelper;
use AbterPhp\Framework\I18n\MockTranslatorFactory;
use PHPUnit\Framework\TestCase;

class ButtonTest extends TestCase
{
    /**
     * @return array
     */
    public function renderProvider()
    {
        $attributes = StubAttributeFactory::createAttributes();
        $str        = ArrayHelper::toAttributes($attributes);

        return [
            'simple'               => ['Button', [], [], null, null, "<button>Button</button>"],
            'with attributes'      => ['Button', [], $attributes, null, null, "<button$str>Button</button>"],
            'missing translations' => ['Button', [], [], [], null, "<button>Button</button>"],
            'custom tag'           => ['Button', [], [], null, 'mybutton', "<mybutton>Button</mybutton>"],
            'with translations'    => ['Button', [], [], ['Button' => 'Gomb'], null, "<button>Gomb</button>"],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param mixed       $content
     * @param array       $intents
     * @param array       $attributes
     * @param array|null  $translations
     * @param string|null $tag
     * @param string      $expectedResult
     */
    public function testRender(
        $content,
        array $intents,
        array $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ) {
        $sut = $this->createElement($content, $intents, $attributes, $translations, $tag);

        $actualResult1 = (string)$sut;
        $actualResult2 = (string)$sut;

        $this->assertSame($expectedResult, $actualResult1);
        $this->assertSame($expectedResult, $actualResult2);
    }

    /**
     * @param mixed       $content
     * @param array       $intents
     * @param array       $attributes
     * @param array|null  $translations
     * @param string|null $tag
     *
     * @return Button
     */
    protected function createElement(
        $content,
        array $intents,
        array $attributes,
        ?array $translations,
        ?string $tag
    ): Button {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $button = new Button($content, $intents, $attributes, $tag);

        $button->setTranslator($translatorMock);

        return $button;
    }
}
