<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Component;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Helper\Attributes;
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
        $str     = Attributes::toString($attribs);

        return [
            'simple'           => ['abc', 'ABC', false, null, null, null, "<option value=\"abc\">ABC</option>"],
            'attributes'       => ['abc', 'ABC', false, $attribs, null, null, "<option$str value=\"abc\">ABC</option>"],
            'w/o translations' => ['abc', 'ABC', false, null, [], null, "<option value=\"abc\">ABC</option>",],
            'custom tag'       => ['abc', 'ABC', false, null, null, 'foo', "<foo value=\"abc\">ABC</foo>"],
            'w translations'   => ['abc', 'ABC', false, null, ['ABC' => '+'], null, "<option value=\"abc\">+</option>"],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param string                    $value
     * @param INode[]|INode|string|null $content
     * @param bool                      $isSelected
     * @param array|null                $attributes
     * @param string[]|null             $translations
     * @param string|null               $tag
     * @param string                    $expectedResult
     */
    public function testRender(
        string $value,
        $content,
        bool $isSelected,
        ?array $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ): void {
        $sut = $this->createOption($value, $content, $isSelected, $attributes, $translations, $tag);

        $actualResult1 = (string)$sut;
        $actualResult2 = (string)$sut;

        $this->assertSame($expectedResult, $actualResult1);
        $this->assertSame($expectedResult, $actualResult2);
    }

    public function testRemoveThrowsExceptionWhenTryingToRemoveProtectedAttributes(): void
    {
        $this->expectException(\RuntimeException::class);

        $sut = new Option('foo', 'Foo');

        $sut->removeAttribute(Html5::ATTR_VALUE);
    }

    public function testGetValueReturnsEmptyStringIfValueIsNull(): void
    {
        $sut = new Option('foo', 'Foo');

        $sut->getAttribute(Html5::ATTR_VALUE)->reset();

        $actualResult = $sut->getValue();

        $this->assertSame('', $actualResult);
    }

    /**
     * @param string                       $value
     * @param INode[]|INode|string|null    $content
     * @param bool                         $isSelected
     * @param array<string,Attribute>|null $attributes
     * @param string[]|null                $translations
     * @param string|null                  $tag
     *
     * @return Option
     */
    protected function createOption(
        string $value,
        $content,
        bool $isSelected,
        ?array $attributes,
        ?array $translations,
        ?string $tag
    ): Option {
        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $option = new Option($value, $content, $isSelected, [], $attributes, $tag);

        $option->setTranslator($translatorMock);

        return $option;
    }
}
