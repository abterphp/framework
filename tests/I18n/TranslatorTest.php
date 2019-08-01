<?php

declare(strict_types=1);

namespace AbterPhp\Framework\I18n;

use PHPUnit\Framework\TestCase;

class TranslatorTest extends TestCase
{
    /** @var Translator */
    protected $sut;

    /**
     * @var array
     */
    protected $translationData = [
        'foo' => [
            'bar'        => 'baz',
            'replacable' => 'baz %2$s %1$s',
        ],
    ];

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new Translator($this->translationData);
    }

    /**
     * @return array
     */
    public function translateProvider(): array
    {
        return [
            ['foo:bar', [], 'baz'],
            ['foo:replacable', ['second', 'first'], 'baz first second'],
            ['foo:replacable', ['foo:bar', 'first'], 'baz first baz'],
        ];
    }

    /**
     * @dataProvider translateProvider
     *
     * @param string $expression
     * @param array  $args
     * @param string $expectedResult
     */
    public function testTranslate(string $expression, array $args, string $expectedResult)
    {
        $actualResult = $this->sut->translate($expression, ...$args);

        $this->assertEquals($expectedResult, $actualResult);
    }


    /**
     * @return array
     */
    public function canTranslateProvider(): array
    {
        return [
            ['foo:bar', [], true],
            ['foo', [], false],
            ['foo:bar:baz', [], false],
            ['foo:replacable', ['second', 'first'], true],
            ['foo:replacable', ['foo:bar', 'first'], true],
        ];
    }

    /**
     * @dataProvider canTranslateProvider
     *
     * @param string $expression
     * @param array  $args
     * @param bool   $expectedResult
     */
    public function testCanTranslate(string $expression, array $args, bool $expectedResult)
    {
        $actualResult = $this->sut->canTranslate($expression, ...$args);

        $this->assertEquals($expectedResult, $actualResult);
    }
}
