<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class RendererTest extends TestCase
{
    /** @var Renderer - System Under Test */
    protected Renderer $sut;

    /** @var Template|MockObject */
    protected $templateMock;

    /** @var Factory|MockObject */
    protected $templateFactoryMock;

    public function setUp(): void
    {
        parent::setUp();

        $this->templateFactoryMock = $this->createMock(Factory::class);

        $this->sut = new Renderer($this->templateFactoryMock);
    }

    /**
     * @param array  $subTemplateIds
     * @param string $rendered
     *
     * @return Template|MockObject
     */
    protected function createTemplate(array $subTemplateIds, string $rendered)
    {
        /** @var Template|MockObject $templateMock */
        $templateMock = $this->createMock(Template::class);
        $templateMock->expects($this->any())->method('setVars')->willReturnSelf();
        $templateMock->expects($this->any())->method('setTypes')->willReturnSelf();
        $templateMock->expects($this->any())->method('parse')->willReturn($subTemplateIds);
        $templateMock->expects($this->any())->method('render')->willReturn($rendered);

        return $templateMock;
    }

    /**
     * @return array
     */
    public function hasAllValidLoadersProvider(): array
    {
        return [
            'no-loaders'               => [[], [], true],
            'one-loader-no-changes'    => [['foo' => ['foo0', 'foo1']], ['foo' => false], true],
            'one-loader-with-changes'  => [['foo' => ['foo0', 'foo1']], ['foo' => true], false],
            'two-loaders-no-changes'   => [
                ['foo' => ['foo0', 'foo1'], 'bar' => ['bar0']],
                ['foo' => false, 'bar' => false],
                true,
            ],
            'first-loader-is-changed'  => [
                ['foo' => ['foo0', 'foo1'], 'bar' => ['bar0']],
                ['foo' => true, 'bar' => false],
                false,
            ],
            'second-loader-is-changed' => [
                ['foo' => ['foo0', 'foo1'], 'bar' => ['bar0']],
                ['foo' => false, 'bar' => true],
                false,
            ],
            'missing-loaders'          => [['foo' => ['foo0', 'foo1']], [], false],
        ];
    }

    /**
     * @dataProvider hasAllValidLoadersProvider
     *
     * @param array<string,string[]> $subTemplates
     * @param array<string,bool>     $hasAnyChangedSinceValues
     * @param bool                   $expectedResult
     */
    public function testHasAllValidLoaders(
        array $subTemplates,
        array $hasAnyChangedSinceValues,
        bool $expectedResult
    ): void {
        $date = '2019-04-02';

        foreach ($hasAnyChangedSinceValues as $templateType => $hasAnyChangedSinceValue) {
            /** @var ILoader|MockObject $loaderMock */
            $loaderMock = $this->createMock(ILoader::class);
            $loaderMock->expects($this->any())->method('hasAnyChangedSince')->willReturn($hasAnyChangedSinceValue);

            $this->sut->addLoader($templateType, $loaderMock);
        }

        $actualResult = $this->sut->hasAllValidLoaders($subTemplates, $date);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function renderProvider(): array
    {
        return [
            'no-subtemplates'             => [
                ['rendered' => []],
                [],
                'rendered',
            ],
            'no-entities'                 => [
                ['rendered' => ['foo' => ['foo0']]],
                ['foo' => []],
                'rendered',
            ],
            'empty-subtemplates'          => [
                ['rendered' => ['foo' => ['foo0']]],
                ['foo' => [new Data(), new Data()]],
                'rendered',
            ],
            'subtemplates-with-vars'      => [
                ['rendered' => ['foo' => ['foo0']]],
                ['foo' => [new Data('bar', ['bar0' => 'hello']), new Data()]],
                'rendered',
            ],
            'subtemplates-with-templates' => [
                ['rendered' => ['foo' => ['foo0']]],
                ['foo' => [new Data('bar', [], ['bar0' => 'hello']), new Data()]],
                'rendered',
            ],
        ];
    }

    /**
     * @dataProvider renderProvider
     *
     * @param array  $templateData
     * @param array  $loaderData
     * @param string $expectedResult
     */
    public function testRender(array $templateData, array $loaderData, string $expectedResult): void
    {
        $rawContent = '';
        $vars       = [];

        $templateMock = $this->createMock(Template::class);
        $templateMock->expects($this->any())->method('setVars')->willReturnSelf();
        $templateMock->expects($this->any())->method('setTypes')->willReturnSelf();
        $templateMock->expects($this->any())->method('parse')->willReturn(...array_values($templateData));
        $templateMock->expects($this->any())->method('render')->willReturn(...array_keys($templateData));

        $templateMocks                       = array_fill(0, count($templateData), $templateMock);
        $templateMocks[count($templateData)] = $this->createMock(Template::class);

        $this->templateFactoryMock
            ->expects($this->any())
            ->method('create')
            ->willReturnOnConsecutiveCalls(...$templateMocks);

        foreach ($loaderData as $templateType => $entities) {
            /** @var ILoader|MockObject $loaderMock */
            $loaderMock = $this->createMock(ILoader::class);
            $loaderMock->expects($this->any())->method('load')->willReturn($entities);

            $this->sut->addLoader($templateType, $loaderMock);
        }

        $actualResult = $this->sut->render($rawContent, $vars);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testRenderThrowsExceptionIfLoaderIsNotSetForType(): void
    {
        $this->expectException(RuntimeException::class);

        $templateMock = $this->createTemplate(['foo' => ['foo0']], 'rendered');

        $this->templateFactoryMock
            ->expects($this->once())
            ->method('create')
            ->willReturn($templateMock);

        $this->sut->render('', []);
    }
}
