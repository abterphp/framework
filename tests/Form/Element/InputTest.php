<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Element;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\TestDouble\Html\Component\StubAttributeFactory;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

class InputTest extends TestCase
{
    /**
     * @return array[]
     */
    public function renderProvider(): array
    {
        $attributes = StubAttributeFactory::createAttributes();

        return [
            'simple'               => [
                'abc',
                'bcd',
                'val',
                null,
                null,
                null,
                '<input id="abc" type="text" name="bcd" value="val">',
            ],
            'missing translations' => [
                'abc',
                'bcd',
                'val',
                null,
                [],
                null,
                '<input id="abc" type="text" name="bcd" value="val">',
            ],
            'extra attributes'     => [
                'abc',
                'bcd',
                'val',
                $attributes,
                [],
                null,
                "<input foo=\"foo baz\" bar=\"bar baz\" id=\"abc\" type=\"text\" name=\"bcd\" value=\"val\">",
            ],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param string        $inputId
     * @param string        $name
     * @param string        $value
     * @param array|null    $attributes
     * @param string[]|null $translations
     * @param string|null   $tag
     * @param string        $expectedResult
     */
    public function testRender(
        string $inputId,
        string $name,
        string $value,
        ?array $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ): void {
        $sut = $this->createInput($inputId, $name, $value, $attributes, $translations, $tag);

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;

        $this->assertSame($actualResult, $repeatedResult);
        $this->assertSame($expectedResult, $actualResult);
    }

    public function testSetValueSetsAttribute(): void
    {
        $expectedResult = 'foo';

        $sut = new Textarea('id', 'name');

        $sut->setValue($expectedResult);

        $this->assertEquals($expectedResult, $sut->getValue());
    }

    /**
     * @return array
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

        $sut = new Input('id', 'name');

        $sut->setValue($value);
    }

    public function testRemoveAttributeThrowsExceptionWhenTryingToRemoveProtectedAttributes(): void
    {
        $this->expectException(\RuntimeException::class);

        $sut = new Input('id', 'foo');

        $sut->removeAttribute(Html5::ATTR_NAME);
    }

    public function testGetName(): void
    {
        $expectedResult = 'foo';

        $sut = new Input('id', $expectedResult);

        $actualResult = $sut->getName();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetNameReturnEmptyStringIfAttributeIsNull(): void
    {
        $expectedResult = '';

        $sut = new Input('id', $expectedResult);

        $sut->setAttribute(new Attribute(Html5::ATTR_NAME));

        $actualResult = $sut->getName();

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @param string                       $inputId
     * @param string                       $name
     * @param string                       $value
     * @param array<string,Attribute>|null $attributes
     * @param string[]|null                $translations
     * @param string|null                  $tag
     *
     * @return Input
     */
    private function createInput(
        string $inputId,
        string $name,
        string $value,
        ?array $attributes,
        ?array $translations,
        ?string $tag
    ): Input {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $input = new Input($inputId, $name, $value, [], $attributes, $tag);

        $input->setTranslator($translatorMock);

        return $input;
    }
}
