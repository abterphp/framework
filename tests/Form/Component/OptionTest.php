<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Component;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Helper\ArrayHelper;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\TestDouble\Html\Component\StubAttributeFactory;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use PHPUnit\Framework\TestCase;

class OptionTest extends TestCase
{
    /**
     * @return array[]
     */
    public function renderProvider(): array
    {
        $attribs = StubAttributeFactory::createAttributes();
        $str     = ArrayHelper::toAttributes($attribs);

        return [
            'simple'           => ['abc', 'ABC', false, [], null, null, "<option value=\"abc\">ABC</option>"],
            'attributes'       => ['abc', 'ABC', false, $attribs, null, null, "<option$str value=\"abc\">ABC</option>"],
            'w/o translations' => ['abc', 'ABC', false, [], [], null, "<option value=\"abc\">ABC</option>"],
            'custom tag'       => ['abc', 'ABC', false, [], null, 'foo', "<foo value=\"abc\">ABC</foo>"],
            'w translations'   => ['abc', 'ABC', false, [], ['ABC' => '+'], null, "<option value=\"abc\">+</option>"],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param string                    $value
     * @param INode[]|INode|string|null $content
     * @param bool                      $isSelected
     * @param string[][]                $attributes
     * @param string[]|null             $translations
     * @param string|null               $tag
     * @param string                    $expectedResult
     */
    public function testRender(
        string $value,
        $content,
        bool $isSelected,
        array $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ): void {
        $sut = $this->createElement($value, $content, $isSelected, $attributes, $translations, $tag);

        $actualResult1 = (string)$sut;
        $actualResult2 = (string)$sut;

        $this->assertSame($expectedResult, $actualResult1);
        $this->assertSame($expectedResult, $actualResult2);
    }

    /**
     * @param string                    $value
     * @param INode[]|INode|string|null $content
     * @param bool                      $isSelected
     * @param string[][]                $attributes
     * @param string[]|null             $translations
     * @param string|null               $tag
     *
     * @return Option
     */
    protected function createElement(
        string $value,
        $content,
        bool $isSelected,
        array $attributes,
        ?array $translations,
        ?string $tag
    ): Option {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $option = new Option($value, $content, $isSelected, [], $attributes, $tag);

        $option->setTranslator($translatorMock);

        return $option;
    }

    public function testGetValueReturnsEmptyStringIfValueIsUnset(): void
    {
        $sut = new Option('foo', 'Foo');

        $sut->unsetAttribute(Html5::ATTR_VALUE);

        $actualResult = $sut->getValue();

        $this->assertSame('', $actualResult);
    }

    public function testGetValueReturnsEmptyStringIfValueIsNull(): void
    {
        $sut = new Option('foo', 'Foo');

        $sut->setAttribute(Html5::ATTR_VALUE, null);

        $actualResult = $sut->getValue();

        $this->assertSame('', $actualResult);
    }
}
