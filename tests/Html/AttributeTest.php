<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Html;

use PHPUnit\Framework\TestCase;

class AttributeTest extends TestCase
{
    protected const KEY = 'foo';

    /** @var Attribute - System Under Test */
    protected Attribute $sut;

    public function setUp(): void
    {
        $this->sut = new Attribute(static::KEY);
    }

    public function testToString(): void
    {
        $expectedResult = 'foo';

        $actualResult = (string)($this->sut);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testSet(): void
    {
        $expectedResult = 'foo="bar"';

        $this->sut->set('bar');

        $actualResult = (string)($this->sut);

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @return array<string,array>
     */
    public function appendProvider(): array
    {
        return [
            'empty'  => [
                [],
                'foo',
            ],
            'simple' => [
                ['bar'],
                'foo="bar"',
            ],
            'more'   => [
                ['bar', 'baz'],
                'foo="bar baz"',
            ],
            'repeat' => [
                ['bar', 'baz', 'baz', 'bar'],
                'foo="bar baz"',
            ],
        ];
    }

    /**
     * @dataProvider appendProvider
     *
     * @param array  $values
     * @param string $expectedResult
     */
    public function testAppend(array $values, string $expectedResult): void
    {
        $this->sut->append(...$values);

        $actualResult = (string)$this->sut;

        $this->assertSame($expectedResult, $actualResult);
    }

    /**
     * @return array<string,string[]>
     */
    public function removeProvider(): array
    {
        return [
            'simple'     => [['bar', 'baz', 'quix'], 'baz', 1],
            'repeat'     => [['bar', 'baz', 'quix', 'baz'], 'baz', 1],
            'expression' => [['bar', 'baz quix'], 'baz', 0],
        ];
    }

    /**
     * @dataProvider removeProvider
     *
     * @param string[] $values
     * @param string   $removeValue
     * @param int      $expectedResult
     */
    public function testRemove(array $values, string $removeValue, int $expectedResult): void
    {
        $sut          = new Attribute(self::KEY, ...$values);
        $actualResult = $sut->remove($removeValue);

        $this->assertSame($expectedResult, $actualResult);
    }

    public function testReset(): void
    {
        $this->sut->append('foo', 'bar', 'baz');
        $this->sut->reset();

        $actualResult = $this->sut->getValues();

        $this->assertNull($actualResult);
    }

    public function testGetValuesDefault(): void
    {
        $actualResult = $this->sut->getValues();

        $this->assertNull($actualResult);
    }

    public function testGetValuesWithValues(): void
    {
        $this->sut->append('foo', 'bar', 'baz');

        $actualResult = $this->sut->getValues();

        $this->assertSame(['foo', 'bar', 'baz'], $actualResult);
    }
}
