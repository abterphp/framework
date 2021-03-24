<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Validation\Rules;

use PHPUnit\Framework\TestCase;

class MinLengthTest extends TestCase
{
    /**
     * @return array[]
     */
    public function passesProvider(): array
    {
        return [
            'empty'             => ['', [], [1], false],
            'min-1-foo'         => ['foo', [], [1], true],
            'min-3-foo-default' => ['foo', [], [3], true],
            'min-3-foo-inc'     => ['foo', [], [3, true], true],
            'min-3-foo'         => ['foo', [], [3, false], false],
        ];
    }

    /**
     * @dataProvider passesProvider
     *
     * @param       $value
     * @param array $allValues
     * @param array $args
     * @param bool  $expectedResult
     */
    public function testPasses($value, array $allValues, array $args, bool $expectedResult): void
    {
        $sut = new MinLength();
        $sut->setArgs($args);

        $actualResult = $sut->passes($value, $allValues);

        $this->assertEquals($expectedResult, $actualResult);
    }

    public function testGetSlug(): void
    {
        $sut = new MinLength();
        $sut->setArgs([1]);

        $actualResult = $sut->getSlug();

        $this->assertSame('minLength', $actualResult);
    }
}
