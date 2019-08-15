<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Label;

use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use PHPUnit\Framework\TestCase;

class CountableTest extends TestCase
{
    /**
     * @return array
     */
    public function renderProvider()
    {
        return [
            'simple'            => [
                'a',
                'ABC',
                [],
                null,
                null,
                "<label for=\"a\">ABC <span data-count=\"30\" class=\"count\"></span></label>",
            ],
            'with attributes'   => [
                'a',
                'ABC',
                ['foo' => ['bar'], 'class' => ['baz']],
                null,
                null,
                "<label for=\"a\" foo=\"bar\" class=\"baz\">ABC <span data-count=\"30\" class=\"count\"></span></label>", // nolint
            ],
            'with translations' => [
                'a',
                'ABC',
                [],
                ['ABC' => 'CBA'],
                null,
                "<label for=\"a\">CBA <span data-count=\"30\" class=\"count\"></span></label>",
            ],
            'custom tag'        => [
                'a',
                'ABC',
                [],
                [],
                'foo',
                "<foo for=\"a\">ABC <span data-count=\"30\" class=\"count\"></span></foo>",
            ],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param string        $inputId
     * @param string        $content
     * @param array         $attributes
     * @param string[]|null $translations
     * @param string|null   $tag
     * @param string        $expectedResult
     */
    public function testRender(
        string $inputId,
        string $content,
        array $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ) {
        $sut = $this->createElement($inputId, $content, $attributes, $translations, $tag);

        $this->assertSame($expectedResult, (string)$sut);
    }

    /**
     * @param string        $inputId
     * @param string        $content
     * @param array         $attributes
     * @param string[]|null $translations
     * @param string|null   $tag
     *
     * @return Label
     */
    private function createElement(
        string $inputId,
        string $content,
        array $attributes,
        ?array $translations,
        ?string $tag
    ): Countable {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $label = new Countable($inputId, $content, 30, [], $attributes, $tag);

        $label->setTranslator($translatorMock);

        return $label;
    }

    public function testSetTemplateChangesRender()
    {
        $expectedResult = '==||==';

        $sut = new Countable('foo', 'Foo');

        $sut->setTemplate($expectedResult);

        $actualResult = (string)$sut;

        $this->assertStringContainsString($expectedResult, $actualResult);
    }
}
