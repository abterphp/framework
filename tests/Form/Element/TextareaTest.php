<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Element;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Component\StubAttributeFactory;
use AbterPhp\Framework\Html\Helper\ArrayHelper;
use AbterPhp\Framework\I18n\MockTranslatorFactory;

class TextareaTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @return array
     */
    public function renderProvider()
    {
        $attribs = StubAttributeFactory::createAttributes();
        $str     = ArrayHelper::toAttributes($attribs);

        return [
            'simple'               => [
                'abc',
                'bcd',
                'val',
                [],
                null,
                null,
                '<textarea id="abc" rows="3" name="bcd">val</textarea>',
            ],
            'missing translations' => [
                'abc',
                'bcd',
                'val',
                [],
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
                "<textarea$str id=\"abc\" rows=\"3\" name=\"bcd\">val</textarea>",
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
     * @return Textarea
     */
    private function createElement(
        string $inputId,
        string $name,
        string $value,
        array $attributes,
        ?array $translations,
        ?string $tag
    ): Textarea {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $textarea = new Textarea($inputId, $name, $value, [], $attributes, $tag);

        $textarea->setTranslator($translatorMock);

        return $textarea;
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
        $sut = new Textarea('id', 'name');

        $sut->setValue($value);
    }

    public function testGetNameReturnsEmptyStringIfUnset()
    {
        $sut = new Textarea('id', 'name');

        $sut->unsetAttribute(Html5::ATTR_NAME);

        $actualResult = $sut->getName();

        $this->assertSame('', $actualResult);
    }

    public function testGetName()
    {
        $expectedResult = 'foo';

        $sut = new Textarea('id', $expectedResult);

        $actualResult = $sut->getName();

        $this->assertEquals($expectedResult, $actualResult);
    }
}
