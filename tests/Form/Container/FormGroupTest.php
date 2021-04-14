<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Container;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Form\Element\IElement;
use AbterPhp\Framework\Form\Element\Input;
use AbterPhp\Framework\Form\Extra\Help;
use AbterPhp\Framework\Form\Label\Label;
use AbterPhp\Framework\Html\Attributes;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FormGroupTest extends TestCase
{
    /**
     * @return array[]
     */
    public function renderProvider(): array
    {
        return [
            'simple' => ['<foo>', '<bar>', '<baz>', null, null, null, '<div><bar><foo><baz></div>'],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param string          $inputOutput
     * @param string          $labelOutput
     * @param string          $helpOutput
     * @param Attributes|null $attributes
     * @param array|null      $translations
     * @param string|null     $tag
     * @param string          $expectedResult
     */
    public function testRender(
        string $inputOutput,
        string $labelOutput,
        string $helpOutput,
        ?Attributes $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ): void {
        $sut = $this->createElement($inputOutput, $labelOutput, $helpOutput, $attributes, $translations, $tag);

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;

        $this->assertSame($actualResult, $repeatedResult);
        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @param string          $inputOutput
     * @param string          $labelOutput
     * @param string          $helpOutput
     * @param Attributes|null $attributes
     * @param array|null      $translations
     *
     * @param string|null     $tag
     *
     * @return FormGroup
     */
    private function createElement(
        string $inputOutput,
        string $labelOutput,
        string $helpOutput,
        ?Attributes $attributes,
        ?array $translations,
        ?string $tag
    ): FormGroup {
        /** @var IElement|MockObject $inputMock */
        $inputMock = $this->createMock(Input::class);

        /** @var Label|MockObject $labelMock */
        $labelMock = $this->createMock(Label::class);

        /** @var Help|MockObject $helpMock */
        $helpMock = $this->createMock(Help::class);

        $inputMock->expects($this->any())->method('__toString')->willReturn($inputOutput);
        $labelMock->expects($this->any())->method('__toString')->willReturn($labelOutput);
        $helpMock->expects($this->any())->method('__toString')->willReturn($helpOutput);

        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $formGroup = new FormGroup($inputMock, $labelMock, $helpMock, [], $attributes, $tag);

        $formGroup->setTranslator($translatorMock);

        return $formGroup;
    }

    public function testGetExtendedNodesIncludesInputLabelAndHelp(): void
    {
        $input = new Input('foo', 'foo');
        $label = new Label('foo', 'Foo');
        $help  = new Help('help');

        $sut = new FormGroup($input, $label, $help);

        $actualResult = $sut->getExtendedNodes();

        $this->assertContains($input, $actualResult);
        $this->assertContains($label, $actualResult);
        $this->assertContains($help, $actualResult);
    }

    public function testGetElementsReturnsInput(): void
    {
        $input = new Input('foo', 'foo');
        $label = new Label('foo', 'Foo');

        $sut = new FormGroup($input, $label);

        $actualResult = $sut->getElements();

        $this->assertSame([$input], $actualResult);
    }

    public function testSetValueSetsInputValue(): void
    {
        $expectedResult = 'bar';

        $input = new Input('foo', 'foo');
        $label = new Label('foo', 'Foo');
        $help  = new Help('help');

        $sut = new FormGroup($input, $label, $help);

        $sut->setValue($expectedResult);

        $this->assertEquals($expectedResult, $input->getAttribute(Html5::ATTR_VALUE)->getValue());
    }

    public function testSetTemplateChangesRender(): void
    {
        $expectedResult = '==||==';

        $input = new Input('foo', 'foo');
        $label = new Label('foo', 'Foo');
        $help  = new Help('help');

        $sut = new FormGroup($input, $label, $help);

        $sut->setTemplate($expectedResult);

        $actualResult = (string)$sut;

        $this->assertStringContainsString($expectedResult, $actualResult);
    }

    public function testGetInput(): void
    {
        /** @var Input|MockObject $input */
        $input = $this->createMock(Input::class);

        /** @var Label|MockObject $label */
        $label = $this->createMock(Label::class);

        $sut = new FormGroup($input, $label);

        $actualResult = $sut->getInput();

        $this->assertSame($input, $actualResult);
    }

    public function testGetLabel(): void
    {
        /** @var Input|MockObject $input */
        $input = $this->createMock(Input::class);

        /** @var Label|MockObject $label */
        $label = $this->createMock(Label::class);

        $sut = new FormGroup($input, $label);

        $actualResult = $sut->getLabel();

        $this->assertSame($label, $actualResult);
    }

    public function testGetHelp(): void
    {
        /** @var Input|MockObject $input */
        $input = $this->createMock(Input::class);

        /** @var Label|MockObject $label */
        $label = $this->createMock(Label::class);

        /** @var INode|MockObject $help */
        $help = $this->createMock(INode::class);

        $sut = new FormGroup($input, $label, $help);

        $actualResult = $sut->getHelp();

        $this->assertSame($help, $actualResult);
    }
}
