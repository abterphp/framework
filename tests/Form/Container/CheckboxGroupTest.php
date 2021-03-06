<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Container;

use AbterPhp\Framework\Form\Element\Input;
use AbterPhp\Framework\Form\Extra\Help;
use AbterPhp\Framework\Form\Label\Label;
use AbterPhp\Framework\Html\Attribute;
use AbterPhp\Framework\Html\Tag;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CheckboxGroupTest extends TestCase
{
    /**
     * @return array[]
     */
    public function renderProvider(): array
    {
        return [
            'simple' => ['<foo>', '<bar>', '<baz>', null, null, null, '<div><bar></div>'],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param string                       $inputOutput
     * @param string                       $labelOutput
     * @param string                       $helpOutput
     * @param array<string,Attribute>|null $attributes
     * @param string[]|null                $translations
     * @param string|null                  $tag
     * @param string                       $expectedResult
     */
    public function testRenderWillMoveHelpIntoLabel(
        string $inputOutput,
        string $labelOutput,
        string $helpOutput,
        ?array $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ): void {
        $sut = $this->createCheckboxGroup($inputOutput, $labelOutput, $helpOutput, $attributes, $translations, $tag);

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;

        $this->assertSame($actualResult, $repeatedResult);
        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @param string                       $inputOutput
     * @param string                       $labelOutput
     * @param string                       $helpOutput
     * @param array<string,Attribute>|null $attributes
     * @param string[]|null                $translations
     * @param string|null                  $tag
     *
     * @return CheckboxGroup
     */
    protected function createCheckboxGroup(
        string $inputOutput,
        string $labelOutput,
        string $helpOutput,
        ?array $attributes,
        ?array $translations,
        ?string $tag
    ): CheckboxGroup {
        /** @var Input|MockObject $inputMock */
        $inputMock = $this->createMock(Input::class);

        /** @var Label|MockObject $labelMock */
        $labelMock = $this->createMock(Label::class);

        /** @var Help|MockObject $helpMock */
        $helpMock = $this->createMock(Help::class);

        $inputMock->expects($this->any())->method('__toString')->willReturn($inputOutput);
        $labelMock->expects($this->any())->method('__toString')->willReturn($labelOutput);
        $helpMock->expects($this->any())->method('__toString')->willReturn($helpOutput);

        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $checkboxGroup = new CheckboxGroup($inputMock, $labelMock, $helpMock, [], $attributes, $tag);

        $checkboxGroup->setTranslator($translatorMock);

        return $checkboxGroup;
    }

    public function testGetCheckboxSpan(): void
    {
        /** @var Input|MockObject $input */
        $input = $this->createMock(Input::class);

        /** @var Label|MockObject $label */
        $label = $this->createMock(Label::class);

        $sut = new CheckboxGroup($input, $label);

        $actualResult = $sut->getCheckboxSpan();

        $this->assertInstanceOf(Tag::class, $actualResult);
    }
}
