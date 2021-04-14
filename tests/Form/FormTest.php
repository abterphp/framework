<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form;

use AbterPhp\Framework\Html\Attributes;
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
            'simple'     => ['bar', 'baz', null, [], null, '<form action="bar" method="baz"></form>'],
            'custom-tag' => ['bar', 'baz', null, [], 'foo', '<foo action="bar" method="baz"></foo>'],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param string          $action
     * @param string          $method
     * @param Attributes|null $attributes
     * @param string[]|null   $translations
     * @param string|null     $tag
     * @param string          $expectedResult
     */
    public function testRender(
        string $action,
        string $method,
        ?Attributes $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ): void {
        $sut = $this->createElement($action, $method, $attributes, $translations, $tag);

        $this->assertSame($expectedResult, (string)$sut);
    }

    /**
     * @param string          $action
     * @param string          $method
     * @param Attributes|null $attributes
     * @param string[]|null   $translations
     * @param string|null     $tag
     *
     * @return Form
     */
    private function createElement(
        string $action,
        string $method,
        ?Attributes $attributes,
        ?array $translations,
        ?string $tag
    ): Form {
        $translatorMock = $translations ? MockTranslatorFactory::createSimpleTranslator($this, $translations) : null;

        $form = new Form($action, $method, [], $attributes, $tag);

        $form->setTranslator($translatorMock);

        return $form;
    }
}
