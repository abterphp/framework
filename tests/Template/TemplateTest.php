<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Template;

use AbterPhp\Framework\Html\Helper\Attributes;
use PHPUnit\Framework\TestCase;

class TemplateTest extends TestCase
{
    /** @var Template - System Under Test */
    protected Template $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new Template();
    }

    /**
     * @return array
     */
    public function parsingSuccessProvider(): array
    {
        $attributes = Attributes::fromArray(['f' => 'foo', 'b' => 'bar']);

        return [
            'empty'                     => ['', []],
            'fail'                      => ['', []],
            'templates-only-1'          => [
                '{{block/content-one-1}}',
                [
                    'block' =>
                        [
                            'content-one-1' => [
                                new ParsedTemplate('block', 'content-one-1', null, ['{{block/content-one-1}}']),
                            ],
                        ],
                ],
            ],
            'templates-only-2'          => [
                '{{block/content-one-1}} {{  block/content-2-two   }}',
                [
                    'block' => [
                        'content-2-two' => [
                            new ParsedTemplate('block', 'content-2-two', null, ['{{  block/content-2-two   }}']),
                        ],
                        'content-one-1' => [
                            new ParsedTemplate('block', 'content-one-1', null, ['{{block/content-one-1}}']),
                        ],
                    ],
                ],
            ],
            'templates-only-3'          => [
                '{{block/layout-one-1}} {{  block/layout-2-two   }} {{block/layout-one-1 }}',
                [
                    'block' => [
                        'layout-2-two' => [
                            new ParsedTemplate('block', 'layout-2-two', null, ['{{  block/layout-2-two   }}']),
                        ],
                        'layout-one-1' => [
                            new ParsedTemplate(
                                'block',
                                'layout-one-1',
                                null,
                                ['{{block/layout-one-1}}', '{{block/layout-one-1 }}']
                            ),
                        ],
                    ],
                ],
            ],
            'template-with-attribute'   => [
                '{{block/layout-one-1 f="foo" b="bar"}}',
                [
                    'block' => [
                        'layout-one-1' => [
                            new ParsedTemplate(
                                'block',
                                'layout-one-1',
                                $attributes,
                                ['{{block/layout-one-1 f="foo" b="bar"}}']
                            ),
                        ],
                    ],
                ],
            ],
            'templates-with-attributes' => [
                '{{block/layout-one-1 f="foo" b="bar"}} {{block/layout-one-1 b="bar" f="foo"}}', // phpcs:ignore
                [
                    'block' => [
                        'layout-one-1' => [
                            new ParsedTemplate(
                                'block',
                                'layout-one-1',
                                $attributes,
                                [
                                    '{{block/layout-one-1 f="foo" b="bar"}}',
                                    '{{block/layout-one-1 b="bar" f="foo"}}',
                                ]
                            ),
                        ],
                    ],
                ],
            ],
        ];
    }

    /**
     * @dataProvider parsingSuccessProvider
     *
     * @param string $rawContent
     * @param array  $expectedTemplates
     */
    public function testParse(string $rawContent, array $expectedTemplates): void
    {
        $this->sut->setRawContent($rawContent);

        $actualTemplates = $this->sut->parse();

        $this->assertEquals($expectedTemplates, $actualTemplates);
    }

    /**
     * @return array
     */
    public function renderingSuccessProvider(): array
    {
        return [
            'content-only-1'                        => [
                'abc',
                [],
                [],
                'abc',
            ],
            'content-with-vars-only-1'              => [
                '0  {{var/variable-one-1}} 1',
                ['variable-one-1' => 'abc'],
                [],
                '0  abc 1',
            ],
            'content-with-vars-only-2'              => [
                '0  {{var/variable-one-1}} 1{{var/variable-one-2}}2',
                ['variable-one-1' => 'abc'],
                [],
                '0  abc 12',
            ],
            'content-with-vars-only-3'              => [
                '0  {{var/variable-one-1}} 1{{var/variable-one-2}}2',
                ['variable-one-1' => 'abc', 'variable-one-2' => 'bcd'],
                [],
                '0  abc 1bcd2',
            ],
            'content-with-repeated-vars'            => [
                '0  {{var/variable-one-1}} {{var/variable-one-1}} 1',
                ['variable-one-1' => 'abc'],
                [],
                '0  abc abc 1',
            ],
            'content-with-modified-repeated-vars'   => [
                '0  {{var/variable-one-1}} {{ var/variable-one-1 }} 1',
                ['variable-one-1' => 'abc'],
                [],
                '0  abc abc 1',
            ],
            'content-with-blocks-only-1'            => [
                '0  {{block/one-1}} 1',
                [],
                ['block' => ['one-1' => 'abc']],
                '0  abc 1',
            ],
            'content-with-blocks-only-2'            => [
                '0  {{block/one-1}} 1{{block/two-2-two}}2',
                [],
                ['block' => ['one-1' => 'abc']],
                '0  abc 12',
            ],
            'content-with-blocks-only-3'            => [
                '0  {{block/one-1}} 1{{block/two-2-two}}2',
                [],
                ['block' => ['one-1' => 'abc', 'two-2-two' => 'bcd']],
                '0  abc 1bcd2',
            ],
            'content-with-repeated-blocks'          => [
                '0  {{block/one-1}} {{block/one-1}} 1{{block/two-2-two}}2',
                [],
                ['block' => ['one-1' => 'abc', 'two-2-two' => 'bcd']],
                '0  abc abc 1bcd2',
            ],
            'content-with-modified-repeated-blocks' => [
                '0  {{block/one-1}} {{ block/one-1 }} 1{{block/two-2-two}}2',
                [],
                ['block' => ['one-1' => 'abc', 'two-2-two' => 'bcd']],
                '0  abc abc 1bcd2',
            ],
            'complex-1'                             => [
                '0  {{block/one-1}} {{ block/one-1 }}  {{var/3-threeThree}} 1{{block/two-2-two}}2{{gallery/event-1}} {{ block/two-2-two }}', // phpcs:ignore
                ['3-threeThree' => 'cde'],
                ['block' => ['one-1' => 'abc', 'two-2-two' => 'bcd'], 'gallery' => ['event-1' => 'fgh']],
                '0  abc abc  cde 1bcd2fgh bcd',
            ],
            'complex-without-subtemplate-value'     => [
                '0  {{block/one-1}} {{ block/one-1 }}  {{var/3-threeThree}} 1{{block/two-2-two}}2{{gallery/event-1}} {{ block/two-2-two }}', // phpcs:ignore
                ['3-threeThree' => 'cde'],
                ['block' => ['one-1' => 'abc'], 'gallery' => ['event-1' => 'fgh']],
                '0  abc abc  cde 12fgh ',
            ],
            'complex-without-subtemplate-type'      => [
                '0  {{block/one-1}} {{ block/one-1 }}  {{var/3-threeThree}} 1{{block/two-2-two}}2{{gallery/event-1}} {{ block/two-2-two }}', // phpcs:ignore
                ['3-threeThree' => 'cde'],
                ['block' => ['one-1' => 'abc', 'two-2-two' => 'bcd']],
                '0  abc abc  cde 1bcd2 bcd',
            ],
            'brutal'                                => [
                "0 {{nope/nay}}  {{block/one-1}} {{  block/one-1 }}  {{var/3-threeThree}} 1{{block/two-2-two foo=\"This foo!\" bar=\"That bar!\"}}2{{gallery/event-1}}\n{{ block/two-2-two }}", // phpcs:ignore
                ['3-threeThree' => 'cde'],
                ['block' => ['one-1' => 'abc', 'two-2-two' => 'bcd']],
                "0 {{nope/nay}}  abc abc  cde 1bcd2\nbcd",
            ],
        ];
    }

    /**
     * @dataProvider renderingSuccessProvider
     *
     * @param string $rawContent
     * @param array  $vars
     * @param array  $templateData
     * @param string $expectedResult
     */
    public function testRender(string $rawContent, array $vars, array $templateData, string $expectedResult): void
    {
        $this->sut->setRawContent($rawContent)->setVars($vars)->setTypes(['block', 'gallery']);

        $this->sut->parse();

        $actualResult = $this->sut->render($templateData);

        $this->assertSame($expectedResult, $actualResult);
    }
}
