<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Element;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Form\Component\Option;
use AbterPhp\Framework\Html\Helper\ArrayHelper;
use AbterPhp\Framework\TestDouble\Html\Component\StubAttributeFactory;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use PHPUnit\Framework\TestCase;

class SelectTest extends TestCase
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
                [],
                null,
                null,
                '<select id="abc" name="bcd"></select>',
            ],
            'missing translations' => [
                'abc',
                'bcd',
                'val',
                [],
                [],
                [],
                null,
                '<select id="abc" name="bcd"></select>',
            ],
            'extra attributes'     => [
                'abc',
                'bcd',
                'val',
                [],
                $attribs,
                [],
                null,
                "<select$str id=\"abc\" name=\"bcd\"></select>",
            ],
            'options'              => [
                'abc',
                'bcd',
                'val',
                ['bde' => 'BDE', 'cef' => 'CEF'],
                $attribs,
                [],
                null,
                "<select$str id=\"abc\" name=\"bcd\"><option value=\"bde\">BDE</option>\n<option value=\"cef\">CEF</option></select>", // nolint
            ],
            'option selected'      => [
                'abc',
                'bcd',
                'cef',
                ['bde' => 'BDE', 'cef' => 'CEF'],
                $attribs,
                [],
                null,
                "<select$str id=\"abc\" name=\"bcd\"><option value=\"bde\">BDE</option>\n<option value=\"cef\" selected>CEF</option></select>", // nolint
            ],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param string        $inputId
     * @param string        $name
     * @param string        $value
     * @param string[]      $options
     * @param array         $attributes
     * @param string[]|null $translations
     * @param string|null   $tag
     * @param string        $expectedResult
     */
    public function testRender(
        string $inputId,
        string $name,
        string $value,
        array $options,
        array $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ) {
        $sut = $this->createElement($inputId, $name, $value, $options, $attributes, $translations, $tag);

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;

        $this->assertSame($actualResult, $repeatedResult);
        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @param string        $inputId
     * @param string        $name
     * @param string        $value
     * @param string[]      $options
     * @param array         $attributes
     * @param string[]|null $translations
     * @param string|null   $tag
     *
     * @return Select
     */
    protected function createElement(
        string $inputId,
        string $name,
        string $value,
        array $options,
        array $attributes,
        ?array $translations,
        ?string $tag
    ): Select {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $select = new Select($inputId, $name, [], $attributes, $tag);

        foreach ($options as $k => $v) {
            $select[] = new Option($k, $v, $value == $k);
        }

        $select->setTranslator($translatorMock);

        return $select;
    }

    public function testSetValueSetsOptionsSelected()
    {
        $sut = new Select('id', 'name');

        $option1 = new Option('1', 'foo', true);
        $option2 = new Option('2', 'bar', false);

        $sut[] = $option1;
        $sut[] = $option2;

        $sut->setValue('2');

        $this->assertStringNotContainsString(Html5::ATTR_SELECTED, (string)$option1);
        $this->assertStringContainsString(Html5::ATTR_SELECTED, (string)$option2);
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
     *
     * @param mixed $value
     */
    public function testSetValueThrowsExceptionOnInvalid($value)
    {
        $this->expectException(\InvalidArgumentException::class);

        $sut = new Select('id', 'name');

        $sut->setValue($value);
    }

    public function testGetNameReturnsEmptyStringIfUnset()
    {
        $sut = new Select('id', 'name');

        $sut->unsetAttribute(Html5::ATTR_NAME);

        $actualResult = $sut->getName();

        $this->assertSame('', $actualResult);
    }

    public function testGetName()
    {
        $expectedResult = 'foo';

        $sut = new Select('id', $expectedResult);

        $actualResult = $sut->getName();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetNameReturnEmptyStringIfAttributeIsNull()
    {
        $expectedResult = '';

        $sut = new Select('id', $expectedResult);

        $sut->setAttribute(Html5::ATTR_NAME, null);

        $actualResult = $sut->getName();

        $this->assertEquals($expectedResult, $actualResult);
    }
}
