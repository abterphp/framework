<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class RendererTest extends TestCase
{
    /** @var Renderer - System Under Test */
    protected $sut;

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
     * @param int      $at
     * @param string[] $subTemplateIds
     * @param string   $rendered
     *
     * @return Template|MockObject
     */
    protected function addTemplate(int $at, array $subTemplateIds, string $rendered)
    {
        /** @var Template|MockObject $templateMock */
        $templateMock = $this->createMock(Template::class);
        $templateMock->expects($this->any())->method('setVars')->willReturnSelf();
        $templateMock->expects($this->any())->method('setTypes')->willReturnSelf();
        $templateMock->expects($this->any())->method('parse')->willReturn($subTemplateIds);
        $templateMock->expects($this->any())->method('render')->willReturn($rendered);

        $this->templateFactoryMock->expects($this->at($at))->method('create')->willReturn($templateMock);

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
     * @param array $subTemplates
     * @param array $hasAnyChangedSinceValues
     * @param bool  $expectedResult
     */
    public function testHasAllValidLoaders(array $subTemplates, array $hasAnyChangedSinceValues, bool $expectedResult)
    {
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
    public function testRender(array $templateData, array $loaderData, string $expectedResult)
    {
        $rawContent = '';
        $vars       = [];

        $i = 0;
        foreach ($templateData as $rendered => $subTemplateIds) {
            $this->addTemplate($i++, $subTemplateIds, $rendered);
        }

        foreach ($loaderData as $templateType => $entities) {
            /** @var ILoader|MockObject $loaderMock */
            $loaderMock = $this->createMock(ILoader::class);
            $loaderMock->expects($this->any())->method('load')->willReturn($entities);

            $this->sut->addLoader($templateType, $loaderMock);
        }

        $actualResult = $this->sut->render($rawContent, $vars);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testRenderThrowsExceptionIfLoaderIsNotSetForType()
    {
        $this->expectException(\RuntimeException::class);

        $this->addTemplate(0, ['foo' => ['foo0']], 'rendered');

        $this->sut->render('', []);
    }
}
