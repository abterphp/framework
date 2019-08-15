<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Form\Container;

use AbterPhp\Framework\Constant\Html5;
use AbterPhp\Framework\Form\Element\IElement;
use AbterPhp\Framework\Form\Element\Input;
use AbterPhp\Framework\Form\Extra\Help;
use AbterPhp\Framework\Form\Label\Label;
use AbterPhp\Framework\Html\INode;
use AbterPhp\Framework\TestDouble\I18n\MockTranslatorFactory;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class FormGroupTest extends TestCase
{
    /**
     * @return array
     */
    public function renderProvider()
    {
        return [
            'simple' => ['<foo>', '<bar>', '<baz>', [], null, null, '<div><bar><foo><baz></div>'],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param string      $inputOutput
     * @param string      $labelOutput
     * @param string      $helpOutput
     * @param array       $attributes
     * @param array|null  $translations
     * @param string|null $tag
     * @param string      $expectedResult
     */
    public function testRender(
        string $inputOutput,
        string $labelOutput,
        string $helpOutput,
        array $attributes,
        ?array $translations,
        ?string $tag,
        string $expectedResult
    ) {
        $sut = $this->createElement($inputOutput, $labelOutput, $helpOutput, $attributes, $translations, $tag);

        $actualResult   = (string)$sut;
        $repeatedResult = (string)$sut;

        $this->assertSame($actualResult, $repeatedResult);
        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @param string     $inputOutput
     * @param string     $labelOutput
     * @param string     $helpOutput
     * @param string     $tag
     * @param array      $attributes
     * @param array|null $translations
     *
     * @return FormGroup
     */
    private function createElement(
        string $inputOutput,
        string $labelOutput,
        string $helpOutput,
        array $attributes,
        ?array $translations,
        ?string $tag
    ): FormGroup {
        /** @var IElement|MockObject $inputMock */
        $inputMock = $this->getMockBuilder(Input::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__toString'])
            ->getMock();

        /** @var Label|MockObject $labelMock */
        $labelMock = $this->getMockBuilder(Label::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['__toString'])
            ->getMock();

        /** @var Help|MockObject $helpMock */
        $helpMock = $this->getMockBuilder(Help::class)
            ->onlyMethods(['__toString'])
            ->getMock();

        $inputMock->expects($this->any())->method('__toString')->willReturn($inputOutput);
        $labelMock->expects($this->any())->method('__toString')->willReturn($labelOutput);
        $helpMock->expects($this->any())->method('__toString')->willReturn($helpOutput);

        $translatorMock = MockTranslatorFactory::createSimpleTranslator($this, $translations);

        $formGroup = new FormGroup($inputMock, $labelMock, $helpMock, [], $attributes, $tag);

        $formGroup->setTranslator($translatorMock);

        return $formGroup;
    }

    public function testGetExtendedNodesIncludesInputLabelAndHelp()
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

    public function testGetElementsReturnsInput()
    {
        $input = new Input('foo', 'foo');
        $label = new Label('foo', 'Foo');

        $sut = new FormGroup($input, $label);

        $actualResult = $sut->getElements();

        $this->assertSame([$input], $actualResult);
    }

    public function testSetValueSetsInputValue()
    {
        $expectedResult = 'bar';

        $input = new Input('foo', 'foo');
        $label = new Label('foo', 'Foo');
        $help  = new Help('help');

        $sut = new FormGroup($input, $label, $help);

        $sut->setValue($expectedResult);

        $actualResult = $input->getAttribute(Html5::ATTR_VALUE);

        $this->assertStringContainsString($expectedResult, $actualResult);
    }

    public function testSetTemplateChangesRender()
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

    public function testGetInput()
    {
        /** @var Input|MockObject $input */
        $input = $this->getMockBuilder(Input::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        /** @var Label|MockObject $label */
        $label = $this->getMockBuilder(Label::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $sut = new FormGroup($input, $label);

        $actualResult = $sut->getInput();

        $this->assertSame($input, $actualResult);
    }

    public function testGetLabel()
    {
        /** @var Input|MockObject $input */
        $input = $this->getMockBuilder(Input::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        /** @var Label|MockObject $label */
        $label = $this->getMockBuilder(Label::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        $sut = new FormGroup($input, $label);

        $actualResult = $sut->getLabel();

        $this->assertSame($label, $actualResult);
    }

    public function testGetHelp()
    {
        /** @var Input|MockObject $input */
        $input = $this->getMockBuilder(Input::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        /** @var Label|MockObject $label */
        $label = $this->getMockBuilder(Label::class)
            ->disableOriginalConstructor()
            ->onlyMethods([])
            ->getMock();

        /** @var INode|MockObject $help */
        $help = $this->getMockBuilder(INode::class)
            ->disableOriginalConstructor()
            ->setMethods([])
            ->getMock();

        $sut = new FormGroup($input, $label, $help);

        $actualResult = $sut->getHelp();

        $this->assertSame($help, $actualResult);
    }
}
