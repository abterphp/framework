<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Element;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Form\Component\Option;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Helper\Attributes;
use AbterPhp\Framework\TestDouble\Html\Component\StubAttributeFactory;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use stdClass;

class MultiSelectTest extends TestCase
{
    public function testToStringIsEmptyByDefault(): void
    {
        $sut = new MultiSelect('id', 'name');

        $this->assertStringContainsString('', (string)$sut);
    }

    public function testSetContentThrowsExceptionIfCalledWithNotNull(): void
    {
        $this->expectException(\LogicException::class);

        $sut = new MultiSelect('id', 'name');

        $sut->setContent(12);
    }

    /**
     * @return array[]
     */
    public function renderProvider(): array
    {
        $attribs = StubAttributeFactory::createAttributes();
        $str     = Attributes::toString($attribs);

        return [
            'simple'               => [
                'abc',
                'bcd',
                ['val'],
                [],
                null,
                null,
                null,
                '<select multiple id="abc" name="bcd"></select>',
            ],
            'missing translations' => [
                'abc',
                'bcd',
                ['val'],
                [],
                null,
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
     * @param string                        $inputId
     * @param string                        $name
     * @param string[]                      $value
     * @param array<string,string>          $options
     * @param array<string, Attribute>|null $attributes
     * @param string[]|null                 $translations
     * @param string|null                   $tag
     * @param string                        $expectedResult
     */
    public function testRender(
        string $inputId,
        string $name,
        array $value,
        array $options,
        ?array $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ): void {
        $sut = $this->createMultiSelect($inputId, $name, $value, $options, $attributes, $translations, $tag);

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;

        $this->assertSame($expectedResult, $actualResult);
        $this->assertSame($actualResult, $repeatedResult);
    }

    public function testSetValueSetsOptionsSelected(): void
    {
        $sut = new MultiSelect('id', 'name');

        $option1 = new Option('1', 'foo', true);
        $option2 = new Option('2', 'bar', false);
        $option3 = new Option('3', 'baz', false);

        $sut->add($option1, $option2, $option3);

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
            'stdclass' => [new stdClass()],
            'int'      => [123],
            'bool'     => [false],
            'float'    => [123.53],
            'integers' => [[1, 3, 4]],
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

        $sut = new MultiSelect('id', 'foo');

        $sut->setValue($value);
    }

    public function testRemoveAttributeThrowsExceptionWhenTryingToRemoveProtectedAttributes(): void
    {
        $this->expectException(\RuntimeException::class);

        $sut = new MultiSelect('id', 'foo');

        $sut->removeAttribute(Html5::ATTR_NAME);
    }

    public function testGetName(): void
    {
        $expectedResult = 'foo';

        $sut = new MultiSelect('id', $expectedResult);

        $actualResult = $sut->getName();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetGetValue(): void
    {
        $expectedResult = ['bar', 'ba'];

        $sut = $this->createMultiSelect('id', 'name', $expectedResult, ['foo' => 'Foo', 'bar' => 'Bar', 'ba' => 'Ba']);

        $actualResult = $sut->getValue();

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @param string                       $inputId
     * @param string                       $name
     * @param string[]                     $values
     * @param array<string,string>         $options
     * @param array<string,Attribute>|null $attributes
     * @param string[]|null                $translations
     * @param string|null                  $tag
     *
     * @return MultiSelect
     */
    protected function createMultiSelect(
        string $inputId,
        string $name,
        array $values,
        array $options,
        ?array $attributes = null,
        ?array $translations = null,
        ?string $tag = null
    ): MultiSelect {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $multiSelect = new MultiSelect($inputId, $name, [], $attributes, $tag);

        foreach ($options as $k => $v) {
            $multiSelect->add(new Option($k, $v, in_array($k, $values, true)));
        }

        $multiSelect->setTranslator($translatorMock);

        return $multiSelect;
    }
}
