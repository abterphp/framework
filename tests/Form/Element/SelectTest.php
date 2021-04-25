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
use LogicException;
use PHPUnit\Framework\TestCase;
use stdClass;

class SelectTest extends TestCase
{
    public function testSetContentReturnsSelfIfContentIsNull(): void
    {
        $sut = $this->createSelect('id', 'name', 'bar', ['foo' => 'Foo', 'bar' => 'Bar', 'baz' => 'Baz']);

        $actualResult = $sut->setContent(null);

        $this->assertEquals($sut, $actualResult);
    }

    public function testSetContentThrowsExceptionIfContentIsNotNull(): void
    {
        $this->expectException(LogicException::class);

        $sut = $this->createSelect('id', 'name', 'bar', ['foo' => 'Foo', 'bar' => 'Bar', 'baz' => 'Baz']);

        $sut->setContent(false);
    }

    public function testToStringIsEmptyByDefault(): void
    {
        $sut = new Select('id', 'name');

        $this->assertStringContainsString('', (string)$sut);
    }

    public function testSetContentThrowsExceptionIfCalledWithNotNull(): void
    {
        $this->expectException(LogicException::class);

        $sut = new Select('id', 'name');

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
                'val',
                [],
                null,
                null,
                null,
                '<select id="abc" name="bcd"></select>',
            ],
            'missing translations' => [
                'abc',
                'bcd',
                'val',
                [],
                null,
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
                "<select$str id=\"abc\" name=\"bcd\"><option value=\"bde\">BDE</option>\n<option value=\"cef\">CEF</option></select>", // phpcs:ignore
            ],
            'option selected'      => [
                'abc',
                'bcd',
                'cef',
                ['bde' => 'BDE', 'cef' => 'CEF'],
                $attribs,
                [],
                null,
                "<select$str id=\"abc\" name=\"bcd\"><option value=\"bde\">BDE</option>\n<option value=\"cef\" selected>CEF</option></select>", // phpcs:ignore
            ],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param string               $inputId
     * @param string               $name
     * @param string               $value
     * @param array<string,string> $options
     * @param Attribute[]|null     $attributes
     * @param string[]|null        $translations
     * @param string|null          $tag
     * @param string               $expectedResult
     */
    public function testRender(
        string $inputId,
        string $name,
        string $value,
        array $options,
        ?array $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ): void {
        $sut = $this->createSelect($inputId, $name, $value, $options, $attributes, $translations, $tag);

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;

        $this->assertSame($actualResult, $repeatedResult);
        $this->assertSame($expectedResult, $actualResult);
    }

    public function testSetValueSetsOptionsSelected(): void
    {
        $sut = new Select('id', 'name');

        $option1 = new Option('1', 'foo', true);
        $option2 = new Option('2', 'bar', false);

        $sut->add($option1, $option2);

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

        $sut = new Select('id', 'foo');

        $sut->setValue($value);
    }

    public function testRemoveAttributeThrowsExceptionWhenTryingToRemoveProtectedAttributes(): void
    {
        $this->expectException(\RuntimeException::class);

        $sut = new Select('id', 'foo');

        $sut->removeAttribute(Html5::ATTR_NAME);
    }

    public function testGetName(): void
    {
        $expectedResult = 'foo';

        $sut = new Select('id', $expectedResult);

        $actualResult = $sut->getName();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetGetValueReturnsNullIfNoOptionIsSelected(): void
    {
        $sut = $this->createSelect('id', 'name', 'abc', ['foo' => 'Foo', 'bar' => 'Bar', 'baz' => 'Baz']);

        $actualResult = $sut->getValue();

        $this->assertNull($actualResult);
    }

    public function testGetGetValue(): void
    {
        $expectedResult = 'bar';

        $sut = $this->createSelect('id', 'name', $expectedResult, ['foo' => 'Foo', 'bar' => 'Bar', 'baz' => 'Baz']);

        $actualResult = $sut->getValue();

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetNameReturnEmptyStringIfAttributeIsNull(): void
    {
        $expectedResult = '';

        $sut = new Select('id', 'foo');

        $sut->getAttribute(Html5::ATTR_NAME)->reset();

        $actualResult = $sut->getName();

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @param string               $inputId
     * @param string               $name
     * @param string               $value
     * @param array<string,string> $options
     * @param Attribute[]|null     $attributes
     * @param string[]|null        $translations
     * @param string|null          $tag
     *
     * @return Select
     */
    protected function createSelect(
        string $inputId,
        string $name,
        string $value,
        array $options,
        ?array $attributes = null,
        ?array $translations = null,
        ?string $tag = null
    ): Select {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $select = new Select($inputId, $name, [], $attributes, $tag);

        foreach ($options as $k => $v) {
            $select->add(new Option($k, $v, $value == $k));
        }

        $select->setTranslator($translatorMock);

        return $select;
    }
}
