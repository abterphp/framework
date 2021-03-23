<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Helper;

use PHPUnit\Framework\TestCase;

class ArrayHelperTest extends TestCase
{
    public function toStylesProvider(): array
    {
        return [
            'empty' => [[], ''],
            'one'   => [['color' => 'red'], 'color: red'],
            'three' => [
                ['color' => 'red', 'background' => 'none', 'font-weight' => 'bold'],
                'color: red; background: none; font-weight: bold',
            ],
        ];
    }

    /**
     * @dataProvider toStylesProvider
     *
     * @param array  $styles
     * @param string $expectedResult
     */
    public function testToStyles(array $styles, string $expectedResult)
    {
        $actualResult = ArrayHelper::toStyles($styles);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function toAttributesProvider(): array
    {
        return [
            'empty'     => [[], '', ''],
            'no-prefix' => [
                ['key-only' => null],
                '',
                'key-only',
            ],
            'crazy'     => [
                [
                    'key-only'    => null,
                    'array-value' => ['bar', 'baz'],
                ],
                ' ',
                ' key-only array-value="bar baz"',
            ],
        ];
    }

    /**
     * @dataProvider toAttributesProvider
     *
     * @param array  $attributes
     * @param string $prepend
     * @param string $expectedResult
     */
    public function testToAttributes(array $attributes, string $prepend, string $expectedResult)
    {
        $actualResult = ArrayHelper::toAttributes($attributes, $prepend);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function unsafeMergeAttributesProvider(): array
    {
        $foo     = ['attr1' => ['foo' => 'foo']];
        $bar     = ['attr2' => ['bar' => 'bar']];
        $foo2    = ['attr1' => ['baz' => 'baz']];
        $fooBar  = ['attr1' => ['foo' => 'foo'], 'attr2' => ['bar' => 'bar']];
        $fooFoo2 = ['attr1' => ['foo' => 'foo', 'baz' => 'baz']];

        return [
            'empty'     => [[], [], []],
            'foo-empty' => [$foo, [], $foo],
            'empty-foo' => [[], $foo, $foo],
            'foo-bar'   => [$foo, $bar, $fooBar],
            'foo-foo2'  => [$foo, $foo2, $fooFoo2],
        ];
    }

    /**
     * @dataProvider unsafeMergeAttributesProvider
     *
     * @param array $existingAttributes
     * @param array $newAttributes
     * @param array $expectedResult
     */
    public function testUnsafeMergeAttributes(array $existingAttributes, array $newAttributes, array $expectedResult)
    {
        $actualResult = ArrayHelper::unsafeMergeAttributes($existingAttributes, $newAttributes);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function mergeAttributesProvider(): array
    {
        $simple         = ['foo' => ['bar']];
        $default        = ['foo' => ['bar'], 'baz' => ['bar', 'baz']];
        $default2       = ['foo' => ['baz'], 'baz' => ['bar', 'baz']];
        $simpleResult   = ['foo' => ['bar' => 'bar']];
        $defaultResult  = ['foo' => ['bar' => 'bar'], 'baz' => ['bar' => 'bar', 'baz' => 'baz']];
        $default2Result = ['foo' => ['bar' => 'bar', 'baz' => 'baz'], 'baz' => ['bar' => 'bar', 'baz' => 'baz']];
        //
        $withStringAttr = ['foo' => 'bar'];
        //
        $withNullAttr = ['foo' => null];
        //
        $complexExisting = ['foo' => ['bar'], 'baz' => ['bar', 'baz'], 'quix' => 'quix', 't1' => null, 't2' => null];
        $complexNew      = ['foo' => 'foo', 'baz' => ['bar', 'foo'], 'quix' => 'foo', 't2' => 'foo'];
        $complexExpected = [
            'foo'  => ['bar' => 'bar', 'foo' => 'foo'],
            'baz'  => ['bar' => 'bar', 'baz' => 'baz', 'foo' => 'foo'],
            'quix' => ['quix' => 'quix', 'foo' => 'foo'],
            't1'   => null,
            't2'   => ['foo' => 'foo'],
        ];

        return [
            'empty'                      => [[], [], []],
            'new-only-simple'            => [$simple, [], $simpleResult],
            'old-only-simple'            => [[], $simple, $simpleResult],
            'new-only-default'           => [$default, [], $defaultResult],
            'old-only-default'           => [[], $default, $defaultResult],
            'merged-simple-default'      => [$simple, $default, $defaultResult],
            'merged-default-default2'    => [$default, $default2, $default2Result],
            //
            'new-only-with-string'       => [$withStringAttr, [], $simpleResult],
            'old-only-with-string'       => [[], $withStringAttr, $simpleResult],
            'merged-with-string-default' => [$withStringAttr, $default, $defaultResult],
            'merged-default-with-string' => [$default, $withStringAttr, $defaultResult],
            //
            'new-only-with-null'         => [$withNullAttr, [], $withNullAttr],
            'merged-with-null-default'   => [$withNullAttr, $default, $defaultResult],
            //
            'complex'                    => [$complexExisting, $complexNew, $complexExpected],
        ];
    }

    /**
     * @dataProvider mergeAttributesProvider
     *
     * @param array $existingAttributes
     * @param array $newAttributes
     * @param array $expectedResult
     */
    public function testMergeAttributes(array $existingAttributes, array $newAttributes, array $expectedResult)
    {
        $actualResult = ArrayHelper::mergeAttributes($existingAttributes, $newAttributes);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function formatAttributeProvider(): array
    {
        return [
            'null'               => [null, null],
            'empty-string'       => ['', null],
            'false'              => [false, null],
            'word'               => ['foo', ['foo' => 'foo']],
            'word-wrapped'       => [['foo'], ['foo' => 'foo']],
            'string'             => ['foo bar', ['foo' => 'foo', 'bar' => 'bar']],
            'words'              => [['foo', 'bar'], ['foo' => 'foo', 'bar' => 'bar']],
            'non-unique-string'  => ['foo foo bar', ['foo' => 'foo', 'bar' => 'bar']],
            'strings'            => [['foo', 'foo foo bar'], ['foo' => 'foo', 'bar' => 'bar']],
            'non-unique-strings' => [['foo', 'foo foo bar' => 'foo foo bar'], ['foo' => 'foo', 'bar' => 'bar']],
            'crazy-values'       => [[1, false], ['1' => '1']],
            'tabs-not-checked'   => [["first\tsecond"], ["first\tsecond" => "first\tsecond"]],
        ];
    }

    /**
     * @dataProvider formatAttributeProvider
     *
     * @param mixed      $value
     * @param array|null $expectedResult
     */
    public function testFormatAttribute($value, ?array $expectedResult)
    {
        $actualResult = ArrayHelper::formatAttribute($value);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array
     */
    public function formatAttributeFailureProvider(): array
    {
        return [
            'object'           => [new \stdClass()],
            'array-of-objects' => [[new \stdClass()]],
        ];
    }

    /**
     * @dataProvider formatAttributeFailureProvider
     *
     * @param mixed $value
     */
    public function testFormatAttributeFailure($value)
    {
        $this->expectException(\InvalidArgumentException::class);

        ArrayHelper::formatAttribute($value);
    }

    /**
     * @return array
     */
    public function toQueryProvider(): array
    {
        return [
            'empty'   => [[], ''],
            'simple'  => [['key' => 'value'], '?key=value'],
            'default' => [['key1' => 'value1', 'key2' => 'value2'], '?key1=value1&key2=value2'],
            'complex' => [['key1' => 'value1ő', 'key2' => 'value2'], '?key1=value1%C5%91&key2=value2'],
        ];
    }

    /**
     * @dataProvider toQueryProvider
     *
     * @param array  $parts
     * @param string $expectedResult
     */
    public function testToQuery(array $parts, string $expectedResult)
    {
        $actualResult = ArrayHelper::toQuery($parts);

        $this->assertEquals($expectedResult, $actualResult);
    }
}
