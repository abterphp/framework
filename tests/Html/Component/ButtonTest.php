<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Component;

use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\TestDouble\Html\Component\StubAttributeFactory;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use PHPUnit\Framework\TestCase;

class ButtonTest extends TestCase
{
    public function renderProvider(): array
    {
        $attributes = StubAttributeFactory::createAttributes();

        return [
            'simple'               => ['Button', [], null, null, null, "<button>Button</button>"],
            'with attributes'      => [
                'Button',
                [],
                $attributes,
                null,
                null,
                "<button foo=\"foo baz\" bar=\"bar baz\">Button</button>",
            ],
            'missing translations' => ['Button', [], null, [], null, "<button>Button</button>",],
            'custom tag'           => ['Button', [], null, null, 'mybutton', "<mybutton>Button</mybutton>"],
            'with translations'    => ['Button', [], null, ['Button' => 'Gomb'], null, "<button>Gomb</button>"],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param mixed                         $content
     * @param array                         $intents
     * @param array<string, Attribute>|null $attributes
     * @param array|null                    $translations
     * @param string|null                   $tag
     * @param string                        $expectedResult
     */
    public function testRender(
        $content,
        array $intents,
        ?array $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ): void {
        $sut = $this->createButton($content, $intents, $attributes, $translations, $tag);

        $actualResult1 = (string)$sut;
        $actualResult2 = (string)$sut;

        $this->assertSame($expectedResult, $actualResult1);
        $this->assertSame($expectedResult, $actualResult2);
    }

    /**
     * @param mixed                         $content
     * @param array                         $intents
     * @param array<string, Attribute>|null $attributes
     * @param array|null                    $translations
     * @param string|null                   $tag
     *
     * @return Button
     */
    protected function createButton(
        $content,
        array $intents,
        ?array $attributes,
        ?array $translations,
        ?string $tag
    ): Button {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $button = new Button($content, $intents, $attributes, $tag);

        $button->setTranslator($translatorMock);

        return $button;
    }
}
