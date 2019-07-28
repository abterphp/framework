<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Element;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Component\StubAttributeFactory;
use AbterPhp\Framework\Html\Helper\ArrayHelper;
use AbterPhp\Framework\I18n\MockTranslatorFactory;

class InputTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return array
     */
    public function renderProvider()
    {
        $attributes = StubAttributeFactory::createAttributes();

        $str = ArrayHelper::toAttributes($attributes);

        return [
            'simple'               => [
                'abc',
                'bcd',
                'val',
                [],
                null,
                null,
                '<input id="abc" type="text" name="bcd" value="val">',
            ],
            'missing translations' => [
                'abc',
                'bcd',
                'val',
                [],
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
                "<input$str id=\"abc\" type=\"text\" name=\"bcd\" value=\"val\">",
            ],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param string        $inputId
     * @param string        $name
     * @param string        $value
     * @param string[][]    $attributes
     * @param string[]|null $translations
     * @param string|null   $tag
     * @param string        $expectedResult
     */
    public function testRender(
        string $inputId,
        string $name,
        string $value,
        array $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ) {
        $sut = $this->createElement($inputId, $name, $value, $attributes, $translations, $tag);

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;

        $this->assertSame($actualResult, $repeatedResult);
        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @param string        $inputId
     * @param string        $name
     * @param string        $value
     * @param string[][]    $attributes
     * @param string[]|null $translations
     * @param string|null   $tag
     *
     * @return Input
     */
    private function createElement(
        string $inputId,
        string $name,
        string $value,
        array $attributes,
        ?array $translations,
        ?string $tag
    ): Input {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $input = new Input($inputId, $name, $value, [], $attributes, $tag);

        $input->setTranslator($translatorMock);

        return $input;
    }

    public function testSetValueSetsAttribute()
    {
        $expectedResult = 'foo';

        $sut = new Textarea('id', 'name');

        $sut->setValue($expectedResult);

        $this->assertEquals($sut->getAttribute(Html5::ATTR_VALUE), $expectedResult);
    }

    /**
     * @return array
     */
    public function setValueFailureProvider(): array
    {
        return [
            'array'    => [[]],
            'stdclass' => [new \stdClass()],
            'int'      => [123],
            'bool'     => [false],
            'float'    => [123.53],
        ];
    }

    /**
     * @dataProvider setValueFailureProvider
     * @expectedException \InvalidArgumentException
     *
     * @param mixed $value
     */
    public function testSetValueThrowsExceptionOnInvalid($value)
    {
        $sut = new Input('id', 'name');

        $sut->setValue($value);
    }

    public function testGetNameReturnsEmptyStringIfUnset()
    {
        $sut = new Input('id', 'name');

        $sut->unsetAttribute(Html5::ATTR_NAME);

        $actualResult = $sut->getName();

        $this->assertSame('', $actualResult);
    }

    public function testGetName()
    {
        $expectedResult = 'foo';

        $sut = new Input('id', $expectedResult);

        $actualResult = $sut->getName();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetNameReturnEmptyStringIfAttributeIsNull()
    {
        $expectedResult = '';

        $sut = new Input('id', $expectedResult);

        $sut->setAttribute(Html5::ATTR_NAME, null);

        $actualResult = $sut->getName();

        $this->assertEquals($expectedResult, $actualResult);
    }
}
