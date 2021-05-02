<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html\Helper;

use AbterPhp\Framework\Html\Attribute;
use AssertionError;
use PHPUnit\Framework\TestCase;

class AttributesTest extends TestCase
{
    public function fromArrayProvider(): array
    {
        return [
            'null'                    => [
                null,
                [],
            ],
            'empty'                   => [
                [],
                [],
            ],
            'single-null-attribute'   => [
                ['foo' => null],
                ['foo' => new Attribute('foo')],
            ],
            'single-string-attribute' => [
                ['foo' => 'bar'],
                ['foo' => new Attribute('foo', 'bar')],
            ],
            'single-array-attribute'  => [
                ['foo' => ['bar', 'baz']],
                ['foo' => new Attribute('foo', 'bar', 'baz')],
            ],
            'mixed-attributes'        => [
                ['foo' => ['bar', 'baz'], 'bar' => null],
                ['foo' => new Attribute('foo', 'bar', 'baz'), 'bar' => new Attribute('bar')],
            ],
        ];
    }

    /**
     * @dataProvider fromArrayProvider
     *
     * @param array|null              $attributes
     * @param array<string,Attribute> $expectedResult
     */
    public function testFromArray(?array $attributes, array $expectedResult): void
    {
        $actualResult = Attributes::fromArray($attributes);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testFromArrayThrowExceptionIfItReceivesAnAttribute(): void
    {
        $this->expectException(AssertionError::class);

        $attributes = [
            'key' => new Attribute('key'),
        ];

        Attributes::fromArray($attributes);
    }

    public function testFromArrayThrowsExceptionIfIReceivesANonStringScalar(): void
    {
        $this->expectException(AssertionError::class);

        $attributes = [
            'key' => false,
        ];

        Attributes::fromArray($attributes);
    }

    /**
     * @return array[]
     */
    public function mergeProvider(): array
    {
        $foo0  = new Attribute('foo');
        $foo1  = new Attribute('foo', 'bar');
        $foo2  = new Attribute('foo', 'baz');
        $foo12 = new Attribute('foo', 'bar', 'baz');
        $bar1  = new Attribute('bar', 'foo');
        $bar11 = new Attribute('bar', 'foo', 'foo');

        return [
            'all empty'                  => [[], [], []],
            'combine empty attributes 1' => [['foo' => $foo1], ['foo' => $foo0], ['foo' => $foo1]],
            'combine empty attributes 2' => [['foo' => $foo0], ['foo' => $foo1], ['foo' => $foo1]],
            'combine attributes'         => [['foo' => $foo1], ['bar' => $bar1], ['foo' => $foo1, 'bar' => $bar1]],
            'combine attribute values 1' => [['foo' => $foo1], ['foo' => $foo2], ['foo' => $foo12]],
            'combine attribute values 2' => [
                ['foo' => $foo1, 'bar' => $bar1],
                ['foo' => $foo2, 'bar' => $bar1],
                ['foo' => $foo12, 'bar' => $bar11],
            ],
        ];
    }

    /**
     * @dataProvider mergeProvider
     *
     * @param array<string,Attribute> $attributes
     * @param array<string,Attribute> $attributes2
     * @param array<string,Attribute> $expectedResult
     */
    public function testMerge(array $attributes, array $attributes2, array $expectedResult): void
    {
        $actualResult = Attributes::merge($attributes, $attributes2);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @dataProvider mergeProvider
     *
     * @param array<string,Attribute> $attributes
     * @param array<string,Attribute> $attributes2
     * @param array<string,Attribute> $expectedResult
     */
    public function testMergeItem(array $attributes, array $attributes2, array $expectedResult): void
    {
        $actualResult = Attributes::mergeItem($attributes, ...array_values($attributes2));

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array[]
     */
    public function replaceProvider(): array
    {
        $foo0 = new Attribute('foo');
        $foo1 = new Attribute('foo', 'bar');
        $foo2 = new Attribute('foo', 'baz');
        $bar1 = new Attribute('bar', 'foo');

        return [
            'all empty'                  => [[], [], []],
            'combine empty attributes 1' => [['foo' => $foo1], ['foo' => $foo0], ['foo' => $foo0]],
            'combine empty attributes 2' => [['foo' => $foo0], ['foo' => $foo1], ['foo' => $foo1]],
            'combine attributes'         => [['foo' => $foo1], ['bar' => $bar1], ['foo' => $foo1, 'bar' => $bar1]],
            'combine attribute values 1' => [['foo' => $foo1], ['foo' => $foo2], ['foo' => $foo2]],
            'combine attribute values 2' => [
                ['foo' => $foo1, 'bar' => $bar1],
                ['foo' => $foo2, 'bar' => $bar1],
                ['foo' => $foo2, 'bar' => $bar1],
            ],
        ];
    }

    /**
     * @dataProvider replaceProvider
     *
     * @param array<string,Attribute> $attributes
     * @param array<string,Attribute> $attributes2
     * @param array<string,Attribute> $expectedResult
     */
    public function testReplace(array $attributes, array $attributes2, array $expectedResult): void
    {
        $actualResult = Attributes::replace($attributes, $attributes2);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @dataProvider replaceProvider
     *
     * @param array<string,Attribute> $attributes
     * @param array<string,Attribute> $attributes2
     * @param array<string,Attribute> $expectedResult
     */
    public function testReplaceItem(array $attributes, array $attributes2, array $expectedResult): void
    {
        $actualResult = Attributes::replaceItem($attributes, ...array_values($attributes2));

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array[]
     */
    public function mergeFailureProvider(): array
    {
        $foo0  = new Attribute('foo');
        $foo1  = new Attribute('foo', 'bar');
        $foo2  = new Attribute('foo', 'baz');
        $foo12 = new Attribute('foo', 'bar', 'baz');
        $bar1  = new Attribute('bar', 'foo');
        $bar11 = new Attribute('bar', 'foo', 'foo');

        return [
            'all empty'                  => [[], [], []],
            'combine empty attributes 1' => [['foo' => $foo1], ['foo' => $foo0], ['foo' => $foo1]],
            'combine empty attributes 2' => [['foo' => $foo0], ['foo' => $foo1], ['foo' => $foo1]],
            'combine attributes'         => [['foo' => $foo1], ['bar' => $bar1], ['foo' => $foo1, 'bar' => $bar1]],
            'combine attribute values 1' => [['foo' => $foo1], ['foo' => $foo2], ['foo' => $foo12]],
            'combine attribute values 2' => [
                ['foo' => $foo1, 'bar' => $bar1],
                ['foo' => $foo2, 'bar' => $bar1],
                ['foo' => $foo12, 'bar' => $bar11],
            ],
        ];
    }

    /**
     * @dataProvider mergeFailureProvider
     *
     * @param array<string,Attribute> $attributes
     * @param array<string,Attribute> $attributes2
     * @param array<string,Attribute> $expectedResult
     */
    public function testMergeFailure(array $attributes, array $attributes2, array $expectedResult): void
    {
        $actualResult = Attributes::merge($attributes, $attributes2);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array[]
     */
    public function isEqualProvider(): array
    {
        return [
            'key-mismatch'          => [
                [
                    'foo' => new Attribute('foo'),
                ],
                [
                    'bar' => new Attribute('bar'),
                ],
                false,
            ],
            'value-mismatch'        => [
                [
                    'foo' => new Attribute('foo', 'bar'),
                ],
                [
                    'foo' => new Attribute('foo', 'bar', 'baz'),
                ],
                false,
            ],
            'second-value-mismatch' => [
                [
                    'foo' => new Attribute('foo', 'bar'),
                    'bar' => new Attribute('bar', ''),
                ],
                [
                    'foo' => new Attribute('foo', 'bar'),
                    'bar' => new Attribute('bar'),
                ],
                false,
            ],
            'two-attributes'        => [
                [
                    'foo' => new Attribute('foo', 'bar'),
                    'bar' => new Attribute('bar', 'foo', 'baz'),
                ],
                [
                    'foo' => new Attribute('foo', 'bar'),
                    'bar' => new Attribute('bar', 'foo', 'baz'),
                ],
                true,
            ],
        ];
    }

    /**
     * @dataProvider isEqualProvider
     *
     * @param array<string,Attribute> $attributes
     * @param array<string,Attribute> $attributes2
     * @param bool                    $expectedResult
     */
    public function testIsEqual(array $attributes, array $attributes2, bool $expectedResult): void
    {
        $actualResult = Attributes::isEqual($attributes, $attributes2);

        $this->assertEquals($expectedResult, $actualResult);
    }

    /**
     * @return array[]
     */
    public function toStringProvider(): array
    {
        return [
            'empty'            => [[], ''],
            'null-attribute'   => [['foo' => new Attribute('foo')], ' foo'],
            'empty-attribute'  => [['foo' => new Attribute('foo', '')], ' foo=""'],
            'simple-attribute' => [['foo' => new Attribute('foo', 'bar')], ' foo="bar"'],
            'two-attributes'   => [
                [
                    'foo' => new Attribute('foo', 'bar'),
                    'bar' => new Attribute('bar', 'foo', 'baz'),
                ],
                ' foo="bar" bar="foo baz"',
            ],
        ];
    }

    /**
     * @dataProvider toStringProvider
     *
     * @param array<string,Attribute> $attributes
     * @param string                  $expectedResult
     */
    public function testToString(array $attributes, string $expectedResult): void
    {
        $actualResult = Attributes::toString($attributes);

        $this->assertEquals($expectedResult, $actualResult);
    }
}
