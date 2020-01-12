<?php

declare(strict_types=1);

namespace AbterPhp\Framework\Validation\Rules;

use PHPUnit\Framework\TestCase;

class ExactlyOneTest extends TestCase
{
    /** @var ExactlyOne - System Under Test */
    protected $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->sut = new ExactlyOne();
    }

    /**
     * @return array
     */
    public function passesProvider(): array
    {
        return [
            'non-empty-missing'            => ['foo', [], ['field2']],
            'non-empty-empty'              => ['foo', ['field2' => ''], ['field2']],
            'empty-non-empty'              => ['', ['field2' => 'foo'], ['field2']],
            'first-of-four-others-missing' => ['foo', [], ['a', 'b', 'c']],
            'first-of-four-others-empty'   => ['foo', ['a' => '', 'b' => '', 'c' => ''], ['a', 'b', 'c']],
            'third-of-four-others-missing' => ['', ['b' => 'foo'], ['a', 'b', 'c']],
            'third-of-four-others-empty'   => ['', ['a' => '', 'b' => 'foo', 'c' => ''], ['a', 'b', 'c']],
        ];
    }

    /**
     * @dataProvider passesProvider
     *
     * @param       $value
     * @param array $allValues
     * @param array $args
     */
    public function testPasses($value, array $allValues, array $args)
    {
        $this->sut->setArgs($args);

        $actualResult = $this->sut->passes($value, $allValues);

        $this->assertEquals(true, $actualResult);
    }

    /**
     * @return array
     */
    public function failureProvider(): array
    {
        return [
            'empty-missing'              => ['', [], ['field2']],
            'empty-empty'                => ['', ['field2' => ''], ['field2']],
            'zero-missing'               => ['0', [], ['field2']],
            'zero-zero'                  => ['0', ['field2' => '0'], ['field2']],
            'both-truthy'                => ['foo', ['field2' => 'bar'], ['field2']],
            'two-of-four-others-missing' => ['foo', ['b' => 'foo'], ['a', 'b', 'c']],
            'four-of-four'               => ['foo', ['a' => 'bar', 'b' => 'baz', 'c' => 'quix'], ['a', 'b', 'c']],
        ];
    }

    /**
     * @dataProvider failureProvider
     *
     * @param       $value
     * @param array $allValues
     * @param array $args
     */
    public function testFailures($value, array $allValues, array $args)
    {
        $this->sut->setArgs($args);

        $actualResult = $this->sut->passes($value, $allValues);

        $this->assertEquals(false, $actualResult);
    }

    public function testGetErrorPlaceholders()
    {
        $this->sut->setArgs(['field2', 'field3']);

        $errorPlaceholders = $this->sut->getErrorPlaceholders();

        $this->assertEquals(['other1' => 'field2', 'other2' => 'field3'], $errorPlaceholders);
    }

    public function testSetArgsThrowsExceptionWithoutArgs()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->sut->setArgs([]);
    }

    public function testSetArgsThrowsExceptionWithNonStringArgs()
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->sut->setArgs([123]);
    }

    public function testGetSlug()
    {
        $actualResult = $this->sut->getSlug();

        $this->assertSame('exactlyOne', $actualResult);
    }
}
