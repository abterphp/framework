<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Element;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Form\Component\Option;
use AbterPhp\Framework\Html\Helper\ArrayHelper;
use AbterPhp\Framework\TestDouble\Html\Component\StubAttributeFactory;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class MultiSelectTest extends TestCase
{
    /**
     * @return array[]
     */
    public function renderProvider(): array
    {
        $attribs = StubAttributeFactory::createAttributes();
        $str     = ArrayHelper::toAttributes($attribs);

        return [
            'simple'               => [
                'abc',
                'bcd',
                ['val'],
                [],
                [],
                null,
                null,
                '<select multiple id="abc" name="bcd"></select>',
            ],
            'missing translations' => [
                'abc',
                'bcd',
                ['val'],
                [],
                [],
                [],
                null,
                '<select multiple id="abc" name="bcd"></select>',
            ],
            'extra attributes'     => [
                'abc',
                'bcd',
                ['val'],
                [],
                $attribs,
                [],
                null,
                "<select$str multiple id=\"abc\" name=\"bcd\"></select>",
            ],
            'options'              => [
                'abc',
                'bcd',
                ['val'],
                ['bde' => 'BDE', 'cef' => 'CEF'],
                $attribs,
                [],
                null,
                "<select$str multiple id=\"abc\" name=\"bcd\"><option value=\"bde\">BDE</option>\n<option value=\"cef\">CEF</option></select>", // phpcs:ignore
            ],
            'option selected'      => [
                'abc',
                'bcd',
                ['cef'],
                ['bde' => 'BDE', 'cef' => 'CEF'],
                $attribs,
                [],
                null,
                "<select$str multiple id=\"abc\" name=\"bcd\"><option value=\"bde\">BDE</option>\n<option value=\"cef\" selected>CEF</option></select>", // phpcs:ignore
            ],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param string        $inputId
     * @param string        $name
     * @param string[]      $value
     * @param string[]      $options
     * @param array         $attributes
     * @param string[]|null $translations
     * @param string|null   $tag
     * @param string        $expectedResult
     */
    public function testRender(
        string $inputId,
        string $name,
        array $value,
        array $options,
        array $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ): void {
        $sut = $this->createElement($inputId, $name, $value, $options, $attributes, $translations, $tag);

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;

        $this->assertSame($expectedResult, $actualResult);
        $this->assertSame($actualResult, $repeatedResult);
    }

    /**
     * @param string        $inputId
     * @param string        $name
     * @param string[]      $value
     * @param string[]      $options
     * @param array         $attributes
     * @param string[]|null $translations
     * @param string|null   $tag
     *
     * @return MultiSelect
     */
    protected function createElement(
        string $inputId,
        string $name,
        array $value,
        array $options,
        array $attributes,
        ?array $translations,
        ?string $tag
    ): MultiSelect {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $select = new MultiSelect($inputId, $name, [], $attributes, $tag);

        foreach ($options as $k => $v) {
            $select[] = new Option($k, $v, in_array($k, $value, true));
        }

        $select->setTranslator($translatorMock);

        return $select;
    }

    public function testSetValueSetsOptionsSelected(): void
    {
        $sut = new MultiSelect('id', 'name');

        $option1 = new Option('1', 'foo', true);
        $option2 = new Option('2', 'bar', false);
        $option3 = new Option('3', 'baz', false);

        $sut[] = $option1;
        $sut[] = $option2;
        $sut[] = $option3;

        $sut->setValue(['2', '3']);

        $this->assertStringNotContainsString(Html5::ATTR_SELECTED, (string)$option1);
        $this->assertStringContainsString(Html5::ATTR_SELECTED, (string)$option2);
        $this->assertStringContainsString(Html5::ATTR_SELECTED, (string)$option3);
    }

    /**
     * @return array
     */
    public function setValueFailureProvider(): array
    {
        return [
            'string'   => [''],
            'stdclass' => [new \stdClass()],
            'int'      => [123],
            'bool'     => [false],
            'float'    => [123.53],
            'ints'     => [[1, 3, 4]],
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

        $sut = new MultiSelect('id', 'name');

        $sut->setValue($value);
    }

    public function testGetNameReturnsEmptyStringIfUnset(): void
    {
        $sut = new MultiSelect('id', 'name');

        $sut->unsetAttribute(Html5::ATTR_NAME);

        $actualResult = $sut->getName();

        $this->assertSame('', $actualResult);
    }

    public function testGetName(): void
    {
        $expectedResult = 'foo';

        $sut = new MultiSelect('id', $expectedResult);

        $actualResult = $sut->getName();

        $this->assertEquals($expectedResult, $actualResult);
    }
}
