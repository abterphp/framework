<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Label;

use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Helper\Attributes;
use AbterPhp\Framework\TestDouble\Html\Component\StubAttributeFactory;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use PHPUnit\Framework\TestCase;

class CountableTest extends TestCase
{
    /**
     * @return array[]
     */
    public function renderProvider(): array
    {
        $attributes = StubAttributeFactory::createAttributes();
        $str        = Attributes::toString($attributes);

        return [
            'simple'            => [
                'a',
                'ABC',
                null,
                null,
                null,
                "<label for=\"a\">ABC <span data-count=\"30\" class=\"count\"></span></label>",
            ],
            'with attributes'   => [
                'a',
                'ABC',
                $attributes,
                null,
                null,
                "<label$str for=\"a\">ABC <span data-count=\"30\" class=\"count\"></span></label>", // phpcs:ignore
            ],
            'with translations' => [
                'a',
                'ABC',
                null,
                ['ABC' => 'CBA'],
                null,
                "<label for=\"a\">CBA <span data-count=\"30\" class=\"count\"></span></label>",
            ],
            'custom tag'        => [
                'a',
                'ABC',
                null,
                [],
                'foo',
                "<foo for=\"a\">ABC <span data-count=\"30\" class=\"count\"></span></foo>",
            ],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param string                       $inputId
     * @param string                       $content
     * @param array<string,Attribute>|null $attributes
     * @param string[]|null                $translations
     * @param string|null                  $tag
     * @param string                       $expectedResult
     */
    public function testRender(
        string $inputId,
        string $content,
        ?array $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ): void {
        $sut = $this->createCountable($inputId, $content, $attributes, $translations, $tag);

        $this->assertSame($expectedResult, (string)$sut);
    }

    /**
     * @param string                       $inputId
     * @param string                       $content
     * @param array<string,Attribute>|null $attributes
     * @param string[]|null                $translations
     * @param string|null                  $tag
     *
     * @return Countable
     */
    private function createCountable(
        string $inputId,
        string $content,
        ?array $attributes,
        ?array $translations,
        ?string $tag
    ): Countable {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $label = new Countable($inputId, $content, 30, [], $attributes, $tag);

        $label->setTranslator($translatorMock);

        return $label;
    }

    public function testSetTemplateChangesRender(): void
    {
        $expectedResult = '==||==';

        $sut = new Countable('foo', 'Foo');

        $sut->setTemplate($expectedResult);

        $actualResult = (string)$sut;

        $this->assertStringContainsString($expectedResult, $actualResult);
    }
}
