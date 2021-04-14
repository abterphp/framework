<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Element;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Attributes;
use AbterPhp\Framework\TestDouble\Html\Component\StubAttributeFactory;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

class TextareaTest extends TestCase
{
    /**
     * @return array[]
     */
    public function renderProvider(): array
    {
        $attribs = StubAttributeFactory::createAttributes();

        return [
            'simple'               => [
                'abc',
                'bcd',
                'val',
                null,
                null,
                null,
                '<textarea id="abc" rows="3" name="bcd">val</textarea>',
            ],
            'missing translations' => [
                'abc',
                'bcd',
                'val',
                null,
                [],
                null,
                '<textarea id="abc" rows="3" name="bcd">val</textarea>',
            ],
            'extra attributes'     => [
                'abc',
                'bcd',
                'val',
                $attribs,
                [],
                null,
                "<textarea$attribs id=\"abc\" rows=\"3\" name=\"bcd\">val</textarea>",
            ],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param string          $inputId
     * @param string          $name
     * @param string          $value
     * @param Attributes|null $attributes
     * @param string[]|null   $translations
     * @param string|null     $tag
     * @param string          $expectedResult
     */
    public function testRender(
        string $inputId,
        string $name,
        string $value,
        ?Attributes $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ): void {
        $sut = $this->createElement($inputId, $name, $value, $attributes, $translations, $tag);

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;

        $this->assertSame($actualResult, $repeatedResult);
        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @param string          $inputId
     * @param string          $name
     * @param string          $value
     * @param Attributes|null $attributes
     * @param string[]|null   $translations
     * @param string|null     $tag
     *
     * @return Textarea
     */
    private function createElement(
        string $inputId,
        string $name,
        string $value,
        ?Attributes $attributes,
        ?array $translations,
        ?string $tag
    ): Textarea {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $textarea = new Textarea($inputId, $name, $value, [], $attributes, $tag);

        $textarea->setTranslator($translatorMock);

        return $textarea;
    }

    public function testSetValueSetsAttribute(): void
    {
        $expectedResult = 'foo';

        $sut = new Textarea('id', 'name');

        $sut->setValue($expectedResult);

        $this->assertEquals($expectedResult, $sut->getAttribute(Html5::ATTR_VALUE)->getValue());
    }

    /**
     * @return array[]
     */
    public function setValueFailureProvider(): array
    {
        return [
            'array'    => [[]],
            'stdclass' => [new stdClass()],
            'int'      => [123],
            'bool'     => [false],
            'float'    => [123.53],
        ];
    }

    /**
     * @dataProvider setValueFailureProvider
     *
     * @param mixed $value
     */
    public function testSetValueThrowsExceptionOnInvalid($value): void
    {
        $this->expectException(InvalidArgumentException::class);

        $sut = new Textarea('id', 'name');

        $sut->setValue($value);
    }

    public function testGetNameReturnsEmptyStringIfUnset(): void
    {
        $sut = new Textarea('id', 'name');

        $sut->getAttributes()->remove(Html5::ATTR_NAME);

        $actualResult = $sut->getName();

        $this->assertSame('', $actualResult);
    }

    public function testGetName(): void
    {
        $expectedResult = 'foo';

        $sut = new Textarea('id', $expectedResult);

        $actualResult = $sut->getName();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetNameReturnEmptyStringIfAttributeIsNull(): void
    {
        $expectedResult = '';

        $sut = new Textarea('id', $expectedResult);

        $sut->getAttributes()->replaceItem(new Attribute(Html5::ATTR_NAME));

        $actualResult = $sut->getName();

        $this->assertEquals($expectedResult, $actualResult);
    }
}
