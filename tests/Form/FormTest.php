<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form;

use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use PHPUnit\Framework\TestCase;

class FormTest extends TestCase
{
    /**
     * @return array[]
     */
    public function renderProvider(): array
    {
        return [
            'simple'     => ['bar', 'baz', [], [], null, '<form action="bar" method="baz"></form>'],
            'custom-tag' => ['bar', 'baz', [], [], 'foo', '<foo action="bar" method="baz"></foo>'],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param string        $action
     * @param string        $method
     * @param string[][]    $attributes
     * @param string[]|null $translations
     * @param string|null   $tag
     * @param string        $expectedResult
     */
    public function testRender(
        string $action,
        string $method,
        array $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ): void {
        $sut = $this->createElement($action, $method, $attributes, $translations, $tag);

        $this->assertSame($expectedResult, (string)$sut);
    }

    /**
     * @param string        $action
     * @param string        $method
     * @param string[][]    $attributes
     * @param string[]|null $translations
     * @param string|null   $tag
     *
     * @return Form
     */
    private function createElement(
        string $action,
        string $method,
        array $attributes,
        ?array $translations,
        ?string $tag
    ): Form {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $form = new Form($action, $method, [], $attributes, $tag);

        $form->setTranslator($translatorMock);

        return $form;
    }
}
